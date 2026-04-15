<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\ProductMaterial\Delete;

use App\Modules\Product\Entity\ProductMaterial\ProductMaterialRepository;

final readonly class DeleteProductMaterialHandler
{
    public function __construct(
        private ProductMaterialRepository $repository,
    ) {}

    public function handle(DeleteProductMaterialCommand $command): void
    {
        $link = $this->repository->getById($command->id);

        $this->repository->remove($link);
    }
}
