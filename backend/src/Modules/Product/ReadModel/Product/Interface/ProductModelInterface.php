<?php

declare(strict_types=1);

namespace App\Modules\Product\ReadModel\Product\Interface;

interface ProductModelInterface
{
    public function getId(): int;

    /**
     * @return array{
     *     id: int,
     *     name: string
     * }
     */
    public function toArray(): array;
}
