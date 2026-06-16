<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderFile\Delete;

use App\Components\Flusher\FlusherInterface;
use App\Modules\Order\Command\OrderFile\Remove\RemoveOrderFileCommand;
use App\Modules\Order\Command\OrderFile\Remove\RemoveOrderFileHandler;
use App\Modules\Order\Permission\OrderPermission;
use App\Modules\Order\Service\OrderPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class DeleteOrderFileHandler
{
    public function __construct(
        private OrderPermissionService $permissionService,
        private RemoveOrderFileHandler $removeHandler,
        private FlusherInterface $flusher,
    ) {}

    public function handle(DeleteOrderFileCommand $command): void
    {
        $this->permissionService->checkRole(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: OrderPermission::UPDATE,
        );

        $this->removeHandler->handle(new RemoveOrderFileCommand($command->id));
        $this->flusher->flush();
    }
}
