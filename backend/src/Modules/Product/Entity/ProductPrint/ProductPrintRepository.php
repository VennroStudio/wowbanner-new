<?php

declare(strict_types=1);

namespace App\Modules\Product\Entity\ProductPrint;

interface ProductPrintRepository
{
    public function getById(int $id): ProductPrint;

    public function findById(int $id): ?ProductPrint;

    /**
     * @return list<ProductPrint>
     */
    public function findByProductId(int $productId): array;

    public function add(ProductPrint $productPrint): void;

    public function remove(ProductPrint $productPrint): void;
}
