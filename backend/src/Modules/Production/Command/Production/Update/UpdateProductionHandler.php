<?php

declare(strict_types=1);

namespace App\Modules\Production\Command\Production\Update;

use App\Components\Cacher\Cacher;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Production\Entity\Production\ProductionRepository;
use App\Modules\Production\Permission\ProductionPermission;
use App\Modules\Production\Service\ProductionMaterialSyncerService;
use App\Modules\Production\Service\ProductionPermissionService;
use App\Modules\Production\Service\ProductionPrintSyncerService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class UpdateProductionHandler
{
    public function __construct(
        private ProductionRepository $repository,
        private FlusherInterface $flusher,
        private ProductionPermissionService $permissionService,
        private ProductionMaterialSyncerService $materialSyncer,
        private ProductionPrintSyncerService $printSyncer,
        private Cacher $cacher,
    ) {}

    public function handle(UpdateProductionCommand $command): void
    {
        $this->permissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: ProductionPermission::UPDATE,
        );

        $production = $this->repository->getById($command->id);

        $production->edit(name: $command->name);

        $this->materialSyncer->sync($command->id, $command->materials);
        $this->printSyncer->sync($command->id, $command->prints);

        $this->cacher->delete('production_by_id_' . $command->id);

        $this->flusher->flush();
    }
}
