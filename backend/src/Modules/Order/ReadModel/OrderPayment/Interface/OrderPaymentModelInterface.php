<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderPayment\Interface;

interface OrderPaymentModelInterface
{
    public function getId(): int;
    public function toArray(): array;
}
