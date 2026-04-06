<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialImage\Delete;

use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Components\Storage\StorageInterface;
use App\Modules\Material\Entity\MaterialImage\MaterialImageRepository;
use App\Modules\Material\Permission\MaterialPermission;
use App\Modules\Material\Service\MaterialPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class DeleteMaterialImageHandler
{
    public function __construct(
        private MaterialImageRepository $materialImageRepository,
        private MaterialPermissionService $materialPermissionService,
        private StorageInterface $storage,
        private FlusherInterface $flusher,
    ) {}

    /** @throws AccessDeniedException */
    public function handle(DeleteMaterialImageCommand $command): void
    {
        $this->materialPermissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: MaterialPermission::DELETE,
        );

        $image = $this->materialImageRepository->getById($command->materialImageId);

        $this->storage->delete($image->path);
        $this->materialImageRepository->remove($image);

        $this->flusher->flush();
    }
}
