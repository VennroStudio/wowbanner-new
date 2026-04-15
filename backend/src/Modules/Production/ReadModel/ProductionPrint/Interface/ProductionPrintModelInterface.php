<?php

declare(strict_types=1);

namespace App\Modules\Production\ReadModel\ProductionPrint\Interface;

interface ProductionPrintModelInterface
{
    public function getId(): int;

    public function getProductionId(): int;

    /**
     * @return array{
     *     id: int,
     *     production_id: int,
     *     print_id: int
     * }
     */
    public function toArray(): array;
}
