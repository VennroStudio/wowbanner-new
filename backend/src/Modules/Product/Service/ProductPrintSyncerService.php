<?php

declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Command\ProductPrint\Create\CreateProductPrintCommand;
use App\Modules\Product\Command\ProductPrint\Create\CreateProductPrintHandler;
use App\Modules\Product\Command\ProductPrint\Delete\DeleteProductPrintCommand;
use App\Modules\Product\Command\ProductPrint\Delete\DeleteProductPrintHandler;
use App\Modules\Product\Command\ProductPrint\Update\UpdateProductPrintCommand;
use App\Modules\Product\Command\ProductPrint\Update\UpdateProductPrintHandler;
use App\Modules\Product\Entity\ProductPrint\ProductPrintRepository;
use App\Modules\Product\ReadModel\ProductPrint\ProductPrintItem;

final readonly class ProductPrintSyncerService
{
    public function __construct(
        private ProductPrintRepository    $repository,
        private CreateProductPrintHandler $createHandler,
        private UpdateProductPrintHandler $updateHandler,
        private DeleteProductPrintHandler $deleteHandler,
    ) {}

    /**
     * @param list<ProductPrintItem> $items
     */
    public function sync(int $productId, array $items): void
    {
        $currentRows = $this->repository->findByProductId($productId);
        $currentIds = array_map(static fn($p) => $p->id, $currentRows);
        $commandIds = array_filter(array_map(static fn($p) => $p->id, $items));

        foreach ($currentRows as $row) {
            if ($row->id === null) {
                continue;
            }
            if (!\in_array($row->id, $commandIds, true)) {
                $this->deleteHandler->handle(new DeleteProductPrintCommand($row->id));
            }
        }

        foreach ($items as $item) {
            if ($item->id !== null && \in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(new UpdateProductPrintCommand(
                    id: $item->id,
                    productId: $productId,
                    printId: $item->printId,
                ));
            } else {
                $this->createHandler->handle(new CreateProductPrintCommand(
                    productId: $productId,
                    printId: $item->printId,
                ));
            }
        }
    }
}
