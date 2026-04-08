<?php

declare(strict_types=1);

namespace App\Modules\Processing\Command\ProcessingImage\Delete;

use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Processing\Entity\ProcessingImage\ProcessingImageRepository;
use App\Modules\Processing\Permission\ProcessingPermission;
use App\Modules\Processing\Service\ProcessingPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class DeleteProcessingImageHandler
{
    public function __construct(
        private ProcessingImageRepository $repository,
        private ProcessingPermissionService $permissionService,
        private FlusherInterface $flusher,
    ) {}

    /** @throws AccessDeniedException */
    public function handle(DeleteProcessingImageCommand $command): void
    {
        $this->permissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: ProcessingPermission::UPDATE, // Deleting images is part of updating processing
        );

        $image = $this->repository->getById($command->id);

        $this->repository->remove($image);
        $this->flusher->flush();
    }
}
