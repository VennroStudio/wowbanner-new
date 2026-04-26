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
    public function findByProductId(int $productId): array;

    public function add(ProductMaterial $productMaterial): void;

    public function remove(ProductMaterial $productMaterial): void;
}
