<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\ProductMaterial\Create;

use App\Components\Cacher\Cacher;
use App\Modules\Product\Entity\ProductMaterial\ProductMaterial;
use App\Modules\Product\Entity\ProductMaterial\ProductMaterialRepository;

final readonly class CreateProductMaterialHandler
{
    public function __construct(
        private ProductMaterialRepository $repository,
        private Cacher $cacher,
    ) {}

    public function handle(CreateProductMaterialCommand $command): void
    {
        $link = ProductMaterial::create(
            productId: $command->productId,
            materialId: $command->materialId,
            materialOptionId: $command->materialOptionId,
        );

        $this->repository->add($link);
        $this->cacher->deleteTag('product_by_id_' . $command->productId);
    }
}
