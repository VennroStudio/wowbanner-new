<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\Material\Delete;

use App\Components\Cacher\Cacher;
use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Components\Storage\StorageInterface;
use App\Modules\Material\Entity\Material\MaterialRepository;
use App\Modules\Material\Entity\MaterialImage\MaterialImageRepository;
use App\Modules\Material\Permission\MaterialPermission;
use App\Modules\Material\Service\MaterialPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class DeleteMaterialHandler
{
    public function __construct(
        private MaterialRepository $materialRepository,
        private MaterialImageRepository $materialImageRepository,
        private MaterialPermissionService $materialPermissionService,
        private StorageInterface $storage,
        private FlusherInterface $flusher,
        private Cacher $cacher,
    ) {}

    /** @throws AccessDeniedException */
    public function handle(DeleteMaterialCommand $command): void
    {
        $material = $this->materialRepository->getById($command->materialId);

        $this->materialPermissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: MaterialPermission::DELETE,
        );

        $images = $this->materialImageRepository->findByMaterialId($command->materialId);

        foreach ($images as $image) {
            $this->storage->delete($image->path);
            $this->materialImageRepository->remove($image);
        }

        $this->materialRepository->remove($material);

        $this->cacher->delete('material_by_id_' . $command->materialId);

        $this->flusher->flush();
    }
}
