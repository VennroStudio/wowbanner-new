<?php

declare(strict_types=1);

namespace App\Modules\Production\ReadModel\ProductionMaterial\Interface;

interface ProductionMaterialModelInterface
{
    public function getId(): int;

    public function getProductionId(): int;

    /**
     * @return array{
     *     id: int,
     *     production_id: int,
     *     material_option_id: int
     * }
     */
    public function toArray(): array;
}
