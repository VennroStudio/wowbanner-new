<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\ProductMaterial\Update;

use App\Modules\Product\Entity\ProductMaterial\ProductMaterialRepository;

final readonly class UpdateProductMaterialHandler
{
    public function __construct(
        private ProductMaterialRepository $repository,
    ) {}

    public function handle(UpdateProductMaterialCommand $command): void
    {
        $link = $this->repository->getById($command->id);

        $link->edit(
            ProductId: $command->ProductId,
            materialOptionId: $command->materialOptionId,
        );
    }
}
