<?php

declare(strict_types=1);

namespace App\Modules\Production\Service;

use App\Modules\Production\Command\ProductionMaterial\Create\CreateProductionMaterialCommand;
use App\Modules\Production\Command\ProductionMaterial\Create\CreateProductionMaterialHandler;
use App\Modules\Production\Command\ProductionMaterial\Delete\DeleteProductionMaterialCommand;
use App\Modules\Production\Command\ProductionMaterial\Delete\DeleteProductionMaterialHandler;
use App\Modules\Production\Command\ProductionMaterial\Update\UpdateProductionMaterialCommand;
use App\Modules\Production\Command\ProductionMaterial\Update\UpdateProductionMaterialHandler;
use App\Modules\Production\Entity\ProductionMaterial\ProductionMaterialRepository;
use App\Modules\Production\ReadModel\ProductionMaterial\ProductionMaterialItem;

final readonly class ProductionMaterialSyncerService
{
    public function __construct(
        private ProductionMaterialRepository $repository,
        private CreateProductionMaterialHandler $createHandler,
        private UpdateProductionMaterialHandler $updateHandler,
        private DeleteProductionMaterialHandler $deleteHandler,
    ) {}

    /**
     * @param list<ProductionMaterialItem> $items
     */
    public function sync(int $productionId, array $items): void
    {
        $currentRows = $this->repository->findByProductionId($productionId);
        $currentIds = array_map(static fn($m) => $m->id, $currentRows);
        $commandIds = array_filter(array_map(static fn($m) => $m->id, $items));

        foreach ($currentRows as $row) {
            if ($row->id === null) {
                continue;
            }
            if (!\in_array($row->id, $commandIds, true)) {
                $this->deleteHandler->handle(new DeleteProductionMaterialCommand($row->id));
            }
        }

        foreach ($items as $item) {
            if ($item->id !== null && \in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(new UpdateProductionMaterialCommand(
                    id: $item->id,
                    productionId: $productionId,
                    materialOptionId: $item->materialOptionId,
                ));
            } else {
                $this->createHandler->handle(new CreateProductionMaterialCommand(
                    productionId: $productionId,
                    materialOptionId: $item->materialOptionId,
                ));
            }
        }
    }
}
