<?php

declare(strict_types=1);

namespace App\Modules\Processing\Command\Processing\Delete;

use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Components\Storage\StorageInterface;
use App\Modules\Processing\Entity\Processing\ProcessingRepository;
use App\Modules\Processing\Entity\ProcessingImage\ProcessingImageRepository;
use App\Modules\Processing\Permission\ProcessingPermission;
use App\Modules\Processing\Service\ProcessingPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class DeleteProcessingHandler
{
    public function __construct(
        private ProcessingRepository $repository,
        private ProcessingImageRepository $imageRepository,
        private ProcessingPermissionService $permissionService,
        private StorageInterface $storage,
        private FlusherInterface $flusher,
    ) {}

    /** @throws AccessDeniedException */
    public function handle(DeleteProcessingCommand $command): void
    {
        $this->permissionService->checkRole(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: ProcessingPermission::DELETE,
        );

        $processing = $this->repository->getById($command->id);

        foreach ($this->imageRepository->findByProcessingId($command->id) as $image) {
            $this->storage->delete($image->path);
            $this->imageRepository->remove($image);
        }

        $this->repository->remove($processing);
        $this->flusher->flush();
    }
}
