<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderSection\Interface;

interface OrderSectionModelInterface
{
    public function getId(): int;
    public function toArray(): array;
}
