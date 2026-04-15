<?php

declare(strict_types=1);

namespace App\Modules\Production\Entity\ProductionMaterial;

interface ProductionMaterialRepository
{
    public function getById(int $id): ProductionMaterial;

    public function findById(int $id): ?ProductionMaterial;

    /**
     * @return list<ProductionMaterial>
     */
    public function findByProductionId(int $productionId): array;

    public function add(ProductionMaterial $productionMaterial): void;

    public function remove(ProductionMaterial $productionMaterial): void;
}
