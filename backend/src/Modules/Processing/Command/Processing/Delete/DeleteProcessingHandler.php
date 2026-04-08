<?php

declare(strict_types=1);

namespace App\Modules\Processing\Command\Processing\Delete;

use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Processing\Entity\Processing\ProcessingRepository;
use App\Modules\Processing\Permission\ProcessingPermission;
use App\Modules\Processing\Service\ProcessingPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class DeleteProcessingHandler
{
    public function __construct(
        private ProcessingRepository $repository,
        private ProcessingPermissionService $permissionService,
        private FlusherInterface $flusher,
    ) {}

    /** @throws AccessDeniedException */
    public function handle(DeleteProcessingCommand $command): void
    {
        $this->permissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: ProcessingPermission::DELETE,
        );

        $processing = $this->repository->getById($command->id);

        $this->repository->remove($processing);
        $this->flusher->flush();
    }
}
