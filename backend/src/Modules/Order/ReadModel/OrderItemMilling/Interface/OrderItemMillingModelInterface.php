<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderItemMilling\Interface;

interface OrderItemMillingModelInterface
{
    public function getId(): int;
    public function toArray(): array;
}
