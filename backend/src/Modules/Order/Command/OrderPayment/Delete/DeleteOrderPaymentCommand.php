<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderPayment\Delete;

final readonly class DeleteOrderPaymentCommand
{
    public function __construct(
        public int $id,
    ) {}
}
