<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialPricingByPiece\Interface;

interface MaterialPricingByPieceModelInterface
{
    public function getId(): int;

    public function getMaterialId(): int;

    public function getOptionId(): int;

    /**
     * @return array{
     *     id: int,
     *     material_id: int,
     *     option_id: int,
     *     variant_type: array{id: int, label: string},
     *     price: string,
     *     cost: string,
     *     print_hours: string
     * }
     */
    public function toArray(): array;
}
