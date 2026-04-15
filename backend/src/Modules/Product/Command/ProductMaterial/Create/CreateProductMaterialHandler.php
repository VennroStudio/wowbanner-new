<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\ProductMaterial\Create;

use App\Modules\Product\Entity\ProductMaterial\ProductMaterial;
use App\Modules\Product\Entity\ProductMaterial\ProductMaterialRepository;

final readonly class CreateProductMaterialHandler
{
    public function __construct(
        private ProductMaterialRepository $repository,
    ) {}

    public function handle(CreateProductMaterialCommand $command): void
    {
        $link = ProductMaterial::create(
            ProductId: $command->ProductId,
            materialOptionId: $command->materialOptionId,
        );

        $this->repository->add($link);
    }
}
