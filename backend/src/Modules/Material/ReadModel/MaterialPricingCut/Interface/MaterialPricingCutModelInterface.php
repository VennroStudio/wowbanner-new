<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialPricingCut\Interface;

interface MaterialPricingCutModelInterface
{
    public function getId(): int;

    public function getMaterialId(): int;

    public function getOptionId(): int;

    /**
     * @return array{
     *     id: int,
     *     material_id: int,
     *     option_id: int,
     *     type: array{id: int, label: string},
     *     price: string
     * }
     */
    public function toArray(): array;
}
