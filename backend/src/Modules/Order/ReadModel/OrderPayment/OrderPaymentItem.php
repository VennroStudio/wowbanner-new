<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderPayment;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class OrderPaymentItem
{
    public function __construct(
        public ?int $id,
        #[Assert\NotBlank(message: 'validation.order_payment_client_id_required')]
        #[Assert\GreaterThan(0)]
        public int $clientId,
        #[Assert\NotBlank(message: 'validation.order_payment_operation_type_required')]
        public int $operationType,
        #[Assert\NotBlank(message: 'validation.order_payment_payment_type_required')]
        public int $paymentType,
        public ?string $reason = null,
        public ?string $note = null,
        public bool $confirmation = false,
    ) {}
}
