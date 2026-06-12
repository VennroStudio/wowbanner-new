<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\Order\Delete;

use App\Components\Cacher\Cacher;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Order\Entity\Order\OrderRepository;
use App\Modules\Order\Permission\OrderPermission;
use App\Modules\Order\Service\OrderPermissionService;
use App\Modules\Order\Service\OrderStructureDeleteService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class DeleteOrderHandler
{
    public function __construct(
        private OrderRepository $repository,
        private FlusherInterface $flusher,
        private OrderPermissionService $permissionService,
        private Cacher $cacher,
        private OrderStructureDeleteService $structureDeleteService,
    ) {}

    public function handle(DeleteOrderCommand $command): void
    {
        $this->permissionService->checkRole(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: OrderPermission::DELETE,
        );

        $order = $this->repository->getById($command->id);

        $this->structureDeleteService->delete($command->id);

        $this->repository->remove($order);
        $this->cacher->deleteTag('order_by_id_' . $command->id);
        $this->flusher->flush();
    }
}
