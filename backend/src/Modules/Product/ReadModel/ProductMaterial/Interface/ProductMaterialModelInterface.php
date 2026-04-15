<?php

declare(strict_types=1);

namespace App\Modules\Product\ReadModel\ProductMaterial\Interface;

interface ProductMaterialModelInterface
{
    public function getId(): int;

    public function getProductId(): int;

    /**
     * @return array{
     *     id: int,
     *     Product_id: int,
     *     material_option_id: int
     * }
     */
    public function toArray(): array;
}
