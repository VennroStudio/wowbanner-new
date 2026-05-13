<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialOption\Interface;

interface MaterialOptionModelInterface
{
    public function getId(): int;

    public function getMaterialId(): int;

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     material_id: int,
     *     pricingType: array{id: int, label: string},
     *     isCut: bool
     * }
     */
    public function toArray(): array;
}
