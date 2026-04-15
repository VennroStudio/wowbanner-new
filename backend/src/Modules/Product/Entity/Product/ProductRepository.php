<?php

declare(strict_types=1);

namespace App\Modules\Product\Entity\Product;

interface ProductRepository
{
    public function getById(int $id): Product;

    public function findById(int $id): ?Product;

    public function add(Product $Product): void;

    public function remove(Product $Product): void;
}
