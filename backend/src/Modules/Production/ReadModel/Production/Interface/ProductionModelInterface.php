<?php

declare(strict_types=1);

namespace App\Modules\Production\ReadModel\Production\Interface;

interface ProductionModelInterface
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
