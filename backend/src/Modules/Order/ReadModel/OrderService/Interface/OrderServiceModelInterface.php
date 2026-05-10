<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderService\Interface;

interface OrderServiceModelInterface
{
    public function getId(): int;
    public function toArray(): array;
}
