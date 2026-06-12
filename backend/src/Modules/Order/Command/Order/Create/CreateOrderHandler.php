<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\Order\Create;

use App\Components\Flusher\FlusherInterface;
use App\Modules\Order\Entity\Order\Fields\Enums\StatusType;
use App\Modules\Order\Entity\Order\Fields\Enums\StorageType;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderRepository;
use App\Modules\Order\Permission\OrderPermission;
use App\Modules\Order\Service\OrderPermissionService;
use App\Modules\Order\Service\OrderStructureSyncerService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class CreateOrderHandler
{
    public function __construct(
        private OrderRepository $repository,
        private FlusherInterface $flusher,
        private OrderPermissionService $permissionService,
        private OrderStructureSyncerService $structureSyncerService,
    ) {}

    public function handle(CreateOrderCommand $command): void
    {
        $this->permissionService->checkRole(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: OrderPermission::CREATE,
        );

        $order = Order::create(
            creatorId: $command->currentUserId,
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

        $this->repository->add($order);
        $this->flusher->flush();

        $pendingProcessings = $this->structureSyncerService->sync(
            orderId: (int)$order->id,
            delivery: $command->delivery,
            files: $command->files,
            keepFileIds: null,
            items: $command->items,
            millings: $command->millings,
            payments: $command->payments,
            sections: $command->sections,
            services: $command->services,
        );

        $this->flusher->flush();
        $this->structureSyncerService->syncItemProcessings($pendingProcessings);
        $this->flusher->flush();
    }
}
