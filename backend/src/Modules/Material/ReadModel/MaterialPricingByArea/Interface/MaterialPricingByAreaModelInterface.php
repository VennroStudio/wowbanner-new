<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialPricingByArea\Interface;

interface MaterialPricingByAreaModelInterface
{
    public function getId(): int;

    public function getMaterialId(): int;

    public function getOptionId(): int;

    /**
     * @return array{
     *     id: int,
     *     material_id: int,
     *     option_id: int,
     *     dpi_type: array{id: int, label: string},
     *     area_range_type: array{id: int, label: string},
     *     price: string,
     *     cost: string,
     *     print_hours: string
     * }
     */
    public function toArray(): array;
}
