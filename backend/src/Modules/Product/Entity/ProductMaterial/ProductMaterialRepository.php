<?php

declare(strict_types=1);

namespace App\Modules\Product\Entity\ProductMaterial;

interface ProductMaterialRepository
{
    public function getById(int $id): ProductMaterial;

    public function findById(int $id): ?ProductMaterial;

    /**
     * @return list<ProductMaterial>
     */
    public function findByProductId(int $ProductId): array;

    public function add(ProductMaterial $ProductMaterial): void;

    public function remove(ProductMaterial $ProductMaterial): void;
}
