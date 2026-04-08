<?php

declare(strict_types=1);

namespace App\Modules\Processing\Command\ProcessingImage\Update;

use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Processing\Entity\ProcessingImage\ProcessingImageRepository;
use App\Modules\Processing\Permission\ProcessingPermission;
use App\Modules\Processing\Service\ProcessingPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class UpdateProcessingImageHandler
{
    public function __construct(
        private ProcessingImageRepository $repository,
        private ProcessingPermissionService $permissionService,
        private FlusherInterface $flusher,
    ) {}

    /** @throws AccessDeniedException */
    public function handle(UpdateProcessingImageCommand $command): void
    {
        $this->permissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: ProcessingPermission::UPDATE,
        );

        $image = $this->repository->getById($command->id);

        $image->edit(
            path: null, // We keep the existing path
            alt: $command->alt,
        );

        $this->flusher->flush();
    }
}
