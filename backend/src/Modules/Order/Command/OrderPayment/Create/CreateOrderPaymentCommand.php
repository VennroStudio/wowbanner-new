<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderPayment\Create;

final readonly class CreateOrderPaymentCommand
{
    public function __construct(
        public int $orderId,
        public int $clientId,
        public int $operationType,
        public int $paymentType,
        public ?string $reason = null,
        public ?string $note = null,
        public bool $confirmation = false,
    ) {}
}
