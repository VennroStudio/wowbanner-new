<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialPricingByPiece;

interface MaterialPricingByPieceRepository
{
    public function add(MaterialPricingByPiece $materialPricingByPiece): void;

    public function remove(MaterialPricingByPiece $materialPricingByPiece): void;

    public function getById(int $id): MaterialPricingByPiece;

    public function findById(int $id): ?MaterialPricingByPiece;

    /**
     * @return list<MaterialPricingByPiece>
     */
    public function findByMaterialId(int $materialId): array;
}
