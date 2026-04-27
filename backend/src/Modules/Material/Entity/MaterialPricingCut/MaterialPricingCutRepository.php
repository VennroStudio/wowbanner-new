<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialPricingCut;

interface MaterialPricingCutRepository
{
    public function add(MaterialPricingCut $materialPricingCut): void;

    public function remove(MaterialPricingCut $materialPricingCut): void;

    public function getById(int $id): MaterialPricingCut;

    public function findById(int $id): ?MaterialPricingCut;

    /**
     * @return list<MaterialPricingCut>
     */
    public function findByMaterialId(int $materialId): array;
}
