<?php

declare(strict_types=1);

namespace App\Modules\Production\Service;

use App\Modules\Production\Command\ProductionPrint\Create\CreateProductionPrintCommand;
use App\Modules\Production\Command\ProductionPrint\Create\CreateProductionPrintHandler;
use App\Modules\Production\Command\ProductionPrint\Delete\DeleteProductionPrintCommand;
use App\Modules\Production\Command\ProductionPrint\Delete\DeleteProductionPrintHandler;
use App\Modules\Production\Command\ProductionPrint\Update\UpdateProductionPrintCommand;
use App\Modules\Production\Command\ProductionPrint\Update\UpdateProductionPrintHandler;
use App\Modules\Production\Entity\ProductionPrint\ProductionPrintRepository;
use App\Modules\Production\ReadModel\ProductionPrint\ProductionPrintItem;

final readonly class ProductionPrintSyncerService
{
    public function __construct(
        private ProductionPrintRepository $repository,
        private CreateProductionPrintHandler $createHandler,
        private UpdateProductionPrintHandler $updateHandler,
        private DeleteProductionPrintHandler $deleteHandler,
    ) {}

    /**
     * @param list<ProductionPrintItem> $items
     */
    public function sync(int $productionId, array $items): void
    {
        $currentRows = $this->repository->findByProductionId($productionId);
        $currentIds = array_map(static fn($p) => $p->id, $currentRows);
        $commandIds = array_filter(array_map(static fn($p) => $p->id, $items));

        foreach ($currentRows as $row) {
            if ($row->id === null) {
                continue;
            }
            if (!\in_array($row->id, $commandIds, true)) {
                $this->deleteHandler->handle(new DeleteProductionPrintCommand($row->id));
            }
        }

        foreach ($items as $item) {
            if ($item->id !== null && \in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(new UpdateProductionPrintCommand(
                    id: $item->id,
                    productionId: $productionId,
                    printId: $item->printId,
                ));
            } else {
                $this->createHandler->handle(new CreateProductionPrintCommand(
                    productionId: $productionId,
                    printId: $item->printId,
                ));
            }
        }
    }
}
