<?php

declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Command\ProductMaterial\Create\CreateProductMaterialCommand;
use App\Modules\Product\Command\ProductMaterial\Create\CreateProductMaterialHandler;
use App\Modules\Product\Command\ProductMaterial\Delete\DeleteProductMaterialCommand;
use App\Modules\Product\Command\ProductMaterial\Delete\DeleteProductMaterialHandler;
use App\Modules\Product\Command\ProductMaterial\Update\UpdateProductMaterialCommand;
use App\Modules\Product\Command\ProductMaterial\Update\UpdateProductMaterialHandler;
use App\Modules\Product\Entity\ProductMaterial\ProductMaterialRepository;
use App\Modules\Product\ReadModel\ProductMaterial\ProductMaterialItem;

final readonly class ProductMaterialSyncerService
{
    public function __construct(
        private ProductMaterialRepository    $repository,
        private CreateProductMaterialHandler $createHandler,
        private UpdateProductMaterialHandler $updateHandler,
        private DeleteProductMaterialHandler $deleteHandler,
    ) {}

    /**
     * @param list<ProductMaterialItem> $items
     */
    public function sync(int $productId, array $items): void
    {
        $currentRows = $this->repository->findByProductId($productId);
        $currentIds = array_map(static fn($m) => $m->id, $currentRows);
        $commandIds = array_filter(array_map(static fn($m) => $m->id, $items));

        foreach ($currentRows as $row) {
            if ($row->id === null) {
                continue;
            }
            if (!\in_array($row->id, $commandIds, true)) {
                $this->deleteHandler->handle(new DeleteProductMaterialCommand($row->id));
            }
        }

        foreach ($items as $item) {
            if ($item->id !== null && \in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(new UpdateProductMaterialCommand(
                    id: $item->id,
                    productId: $productId,
                    materialOptionId: $item->materialOptionId,
                ));
            } else {
                $this->createHandler->handle(new CreateProductMaterialCommand(
                    productId: $productId,
                    materialOptionId: $item->materialOptionId,
                ));
            }
        }
    }
}
