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
    public function findByProductId(int $ProductId): array;

    public function add(ProductPrint $ProductPrint): void;

    public function remove(ProductPrint $ProductPrint): void;
}
