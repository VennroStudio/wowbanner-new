<?php

declare(strict_types=1);

namespace App\Modules\Product\ReadModel\ProductPrint\Interface;

interface ProductPrintModelInterface
{
    public function getId(): int;

    public function getProductId(): int;

    /**
     * @return array{
     *     id: int,
     *     Product_id: int,
     *     print_id: int
     * }
     */
    public function toArray(): array;
}
