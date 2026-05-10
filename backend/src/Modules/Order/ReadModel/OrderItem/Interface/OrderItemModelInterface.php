<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderItem\Interface;

interface OrderItemModelInterface
{
    public function getId(): int;
    public function toArray(): array;
}
