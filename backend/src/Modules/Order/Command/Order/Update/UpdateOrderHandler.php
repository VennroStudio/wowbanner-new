<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\Order\Update;

use App\Components\Cacher\Cacher;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Order\Entity\Order\Fields\Enums\StatusType;
use App\Modules\Order\Entity\Order\Fields\Enums\StorageType;
use App\Modules\Order\Entity\Order\OrderRepository;
use App\Modules\Order\Permission\OrderPermission;
use App\Modules\Order\Service\OrderPermissionService;
use App\Modules\Order\Service\OrderStructureSyncerService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class UpdateOrderHandler
{
    public function __construct(
        private OrderRepository $repository,
        private FlusherInterface $flusher,
        private OrderPermissionService $permissionService,
        private Cacher $cacher,
        private OrderStructureSyncerService $structureSyncerService,
    ) {}

    public function handle(UpdateOrderCommand $command): void
    {
        $this->permissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: OrderPermission::UPDATE,
        );

        $order = $this->repository->getById($command->id);

        $order->edit(
            managerId: $command->managerId,
            designerId: $command->designerId,
            clientId: $command->clientId,
            statusType: StatusType::from($command->statusType),
            storageType: StorageType::from($command->storageType),
            generalNote: $command->generalNote,
            extension: $command->extension,
            acceptedAt: $command->acceptedAt,
            deadlineAt: $command->deadlineAt,
        );

        $pendingProcessings = $this->structureSyncerService->sync(
            orderId: $command->id,
            delivery: $command->delivery,
            files: $command->files,
            keepFileIds: $command->keepFileIds,
            items: $command->items,
            millings: $command->millings,
            payments: $command->payments,
            sections: $command->sections,
            services: $command->services,
        );

        $this->flusher->flush();
        $this->structureSyncerService->syncItemProcessings($pendingProcessings);
        $this->cacher->delete('order_by_id_' . $command->id);
        $this->flusher->flush();
    }
}
