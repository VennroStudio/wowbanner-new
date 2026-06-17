<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\ProductMaterial\Update;

use App\Components\Cacher\Cacher;
use App\Modules\Product\Entity\ProductMaterial\ProductMaterialRepository;

final readonly class UpdateProductMaterialHandler
{
    public function __construct(
        private ProductMaterialRepository $repository,
        private Cacher $cacher,
    ) {}

    public function handle(UpdateProductMaterialCommand $command): void
    {
        $link = $this->repository->getById($command->id);

        $link->edit(
            productId: $command->productId,
            materialId: $command->materialId,
            materialOptionId: $command->materialOptionId,
        );

        $this->cacher->deleteTag('product_by_id_' . $command->productId);
    }
}
