<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderPayment;

use App\Components\Clock\UtcClock;
use App\Modules\Order\Entity\OrderPayment\Fields\Enums\OperationType;
use App\Modules\Order\Entity\OrderPayment\Fields\Enums\PaymentType;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_payments')]
#[ORM\Index(name: 'idx_order_payment_order_id', columns: ['order_id'])]
#[ORM\Index(name: 'idx_order_payment_client_id', columns: ['client_id'])]
#[ORM\Index(name: 'idx_order_payment_operation_type', columns: ['operation_type'])]
#[ORM\Index(name: 'idx_order_payment_payment_type', columns: ['payment_type'])]
#[ORM\Index(name: 'idx_order_payment_created_at', columns: ['created_at'])]
class OrderPayment
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $orderId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $clientId;

    #[ORM\Column(type: Types::INTEGER, enumType: OperationType::class)]
    private(set) OperationType $operationType;

    #[ORM\Column(type: Types::INTEGER, enumType: PaymentType::class)]
    private(set) PaymentType $paymentType;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private(set) ?string $reason;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private(set) ?string $note;

    #[ORM\Column(type: Types::BOOLEAN)]
    private(set) bool $confirmation;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private(set) DateTimeImmutable $createdAt;

    private function __construct(
        int $orderId,
        int $clientId,
        OperationType $operationType,
        PaymentType $paymentType,
        ?string $reason,
        ?string $note,
        bool $confirmation,
    ) {
        $this->orderId = $orderId;
        $this->clientId = $clientId;
        $this->operationType = $operationType;
        $this->paymentType = $paymentType;
        $this->reason = $reason;
        $this->note = $note;
        $this->confirmation = $confirmation;
        $this->createdAt = UtcClock::now();
    }

    public static function create(
        int $orderId,
        int $clientId,
        OperationType $operationType,
        PaymentType $paymentType,
        ?string $reason,
        ?string $note,
        bool $confirmation,
    ): self {
        return new self(
            orderId: $orderId,
            clientId: $clientId,
            operationType: $operationType,
            paymentType: $paymentType,
            reason: $reason,
            note: $note,
            confirmation: $confirmation,
        );
    }
}
