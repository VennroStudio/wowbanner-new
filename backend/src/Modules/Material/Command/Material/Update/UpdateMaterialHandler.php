<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\Material\Update;

use App\Components\Cacher\Cacher;
use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Material\Entity\Material\MaterialRepository;
use App\Modules\Material\Permission\MaterialPermission;
use App\Modules\Material\Service\MaterialPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class UpdateMaterialHandler
{
    public function __construct(
        private MaterialRepository $materialRepository,
        private MaterialPermissionService $materialPermissionService,
        private FlusherInterface $flusher,
        private Cacher $cacher,
    ) {}

    /** @throws AccessDeniedException */
    public function handle(UpdateMaterialCommand $command): void
    {
        $material = $this->materialRepository->getById($command->materialId);

        $this->materialPermissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: MaterialPermission::UPDATE,
        );

        $material->edit(
            name: $command->name,
            description: $command->description,
        );

        $this->cacher->delete('material_by_id_' . $command->materialId);

        $this->flusher->flush();
    }
}
