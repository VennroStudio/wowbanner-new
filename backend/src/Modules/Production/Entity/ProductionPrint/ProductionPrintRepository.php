<?php

declare(strict_types=1);

namespace App\Modules\Production\Entity\ProductionPrint;

interface ProductionPrintRepository
{
    public function getById(int $id): ProductionPrint;

    public function findById(int $id): ?ProductionPrint;

    /**
     * @return list<ProductionPrint>
     */
    public function findByProductionId(int $productionId): array;

    public function add(ProductionPrint $productionPrint): void;

    public function remove(ProductionPrint $productionPrint): void;
}
