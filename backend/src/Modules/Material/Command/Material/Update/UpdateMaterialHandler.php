<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\Material\Update;

use App\Components\Cacher\Cacher;
use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Material\Entity\Material\MaterialRepository;
use App\Modules\Material\Entity\MaterialOption\MaterialOptionRepository;
use App\Modules\Material\Permission\MaterialPermission;
use App\Modules\Material\Service\MaterialPermissionService;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;
use App\Modules\Material\Service\MaterialStructureSyncerService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class UpdateMaterialHandler
{
    public function __construct(
        private MaterialRepository $materialRepository,
        private MaterialOptionRepository $materialOptionRepository,
        private MaterialPermissionService $materialPermissionService,
        private MaterialStructureSyncerService $materialStructureSyncerService,
        private FlusherInterface $flusher,
        private Cacher $cacher,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
    ) {}

    /** @throws AccessDeniedException */
    public function handle(UpdateMaterialCommand $command): void
    {
        $this->materialPermissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: MaterialPermission::UPDATE,
        );

        $material = $this->materialRepository->getById($command->materialId);

        $material->edit(
            name: $command->name,
            description: $command->description,
        );

        $this->cacher->delete('material_by_id_' . $command->materialId);

        $this->flusher->flush();

        $this->materialStructureSyncerService->sync($command->materialId, $command->options);

        $this->materialQueryCacheInvalidator->invalidateByMaterialId($command->materialId);
        foreach ($this->materialOptionRepository->findByMaterialId($command->materialId) as $option) {
            $this->materialQueryCacheInvalidator->invalidateMaterialOption(
                (int) $option->id,
                $command->materialId
            );
        }
    }
}
