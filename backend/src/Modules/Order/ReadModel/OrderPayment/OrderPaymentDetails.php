<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderPayment;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Order\Entity\OrderPayment\Fields\Enums\OperationType;
use App\Modules\Order\Entity\OrderPayment\Fields\Enums\PaymentType;
use App\Modules\Order\ReadModel\OrderPayment\Interface\OrderPaymentModelInterface;
use Override;

final readonly class OrderPaymentDetails implements OrderPaymentModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $orderId,
        public int $clientId,
        public OperationType $operationType,
        public PaymentType $paymentType,
        public ?string $reason,
        public ?string $note,
        public bool $confirmation,
        public string $createdAt,
    ) {}

    /**
     * @return array<string, string>
     */
    public static function fields(): array
    {
        return [
            'id'             => 'id',
            'order_id'       => 'order_id',
            'client_id'      => 'client_id',
            'operation_type' => 'operation_type',
            'payment_type'   => 'payment_type',
            'reason'         => 'reason',
            'note'           => 'note',
            'confirmation'   => 'confirmation',
            'created_at'     => 'created_at',
        ];
    }

    /**
     * @param array{
     *     id: int,
     *     order_id: int,
     *     client_id: int,
     *     operation_type: int,
     *     payment_type: int,
     *     reason: string|null,
     *     note: string|null,
     *     confirmation: bool|int,
     *     created_at: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            orderId: (int)$row['order_id'],
            clientId: (int)$row['client_id'],
            operationType: OperationType::from((int)$row['operation_type']),
            paymentType: PaymentType::from((int)$row['payment_type']),
            reason: $row['reason'],
            note: $row['note'],
            confirmation: (bool)$row['confirmation'],
            createdAt: $row['created_at'],
        );
    }

    #[Override]
    public function getId(): int
    {
        return $this->id;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'             => $this->id,
            'client_id'      => $this->clientId,
            'operation_type' => ['id' => $this->operationType->value, 'label' => $this->operationType->getLabel()],
            'payment_type'   => ['id' => $this->paymentType->value, 'label' => $this->paymentType->getLabel()],
            'reason'         => $this->reason,
            'note'           => $this->note,
            'confirmation'   => $this->confirmation,
            'created_at'     => $this->createdAt,
        ];
    }
}
