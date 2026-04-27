<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialPricingByArea;

interface MaterialPricingByAreaRepository
{
    public function add(MaterialPricingByArea $materialPricingByArea): void;

    public function remove(MaterialPricingByArea $materialPricingByArea): void;

    public function getById(int $id): MaterialPricingByArea;

    public function findById(int $id): ?MaterialPricingByArea;

    /**
     * @return list<MaterialPricingByArea>
     */
    public function findByMaterialId(int $materialId): array;
}
