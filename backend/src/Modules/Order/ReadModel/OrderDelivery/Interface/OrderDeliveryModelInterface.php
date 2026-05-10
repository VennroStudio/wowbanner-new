<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderDelivery\Interface;

interface OrderDeliveryModelInterface
{
    public function getId(): int;
    public function toArray(): array;
}
