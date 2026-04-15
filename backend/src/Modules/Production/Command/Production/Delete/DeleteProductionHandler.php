<?php

declare(strict_types=1);

namespace App\Modules\Production\Command\Production\Delete;

use App\Components\Cacher\Cacher;
use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Production\Command\ProductionMaterial\Delete\DeleteProductionMaterialCommand;
use App\Modules\Production\Command\ProductionMaterial\Delete\DeleteProductionMaterialHandler;
use App\Modules\Production\Command\ProductionPrint\Delete\DeleteProductionPrintCommand;
use App\Modules\Production\Command\ProductionPrint\Delete\DeleteProductionPrintHandler;
use App\Modules\Production\Entity\Production\ProductionRepository;
use App\Modules\Production\Entity\ProductionMaterial\ProductionMaterialRepository;
use App\Modules\Production\Entity\ProductionPrint\ProductionPrintRepository;
use App\Modules\Production\Permission\ProductionPermission;
use App\Modules\Production\Service\ProductionPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class DeleteProductionHandler
{
    public function __construct(
        private ProductionRepository $repository,
        private ProductionMaterialRepository $productionMaterialRepository,
        private ProductionPrintRepository $productionPrintRepository,
        private FlusherInterface $flusher,
        private ProductionPermissionService $permissionService,
        private DeleteProductionMaterialHandler $deleteProductionMaterialHandler,
        private DeleteProductionPrintHandler $deleteProductionPrintHandler,
        private Cacher $cacher,
    ) {}

    /**
     * @throws AccessDeniedException
     */
    public function handle(DeleteProductionCommand $command): void
    {
        $this->permissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: ProductionPermission::DELETE,
        );

        $production = $this->repository->getById($command->id);

        $this->deleteMaterials($command->id);
        $this->deletePrints($command->id);

        $this->repository->remove($production);

        $this->cacher->delete('production_by_id_' . $command->id);

        $this->flusher->flush();
    }

    private function deleteMaterials(int $productionId): void
    {
        $materials = $this->productionMaterialRepository->findByProductionId($productionId);
        foreach ($materials as $material) {
            if ($material->id === null) {
                continue;
            }
            $this->deleteProductionMaterialHandler->handle(
                new DeleteProductionMaterialCommand($material->id),
            );
        }
    }

    private function deletePrints(int $productionId): void
    {
        $prints = $this->productionPrintRepository->findByProductionId($productionId);
        foreach ($prints as $print) {
            if ($print->id === null) {
                continue;
            }
            $this->deleteProductionPrintHandler->handle(
                new DeleteProductionPrintCommand($print->id),
            );
        }
    }
}
