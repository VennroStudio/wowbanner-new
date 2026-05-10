<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderPayment\Update;

final readonly class UpdateOrderPaymentCommand
{
    public function __construct(
        public int $id,
        public int $clientId,
        public int $operationType,
        public int $paymentType,
        public ?string $reason = null,
        public ?string $note = null,
        public bool $confirmation = false,
    ) {}
}
