<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderNotification;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Order\Entity\OrderNotification\Fields\Enums\NotificationType;
use App\Modules\Order\ReadModel\OrderNotification\Interface\OrderNotificationModelInterface;
use Override;

final readonly class OrderNotificationDetails implements OrderNotificationModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $orderId,
        public NotificationType $notificationType,
        public string $createdAt,
    ) {}

    /**
     * @return array<string, string>
     */
    public static function fields(): array
    {
        return [
            'id'                => 'id',
            'order_id'          => 'order_id',
            'notification_type' => 'notification_type',
            'created_at'        => 'created_at',
        ];
    }

    /**
     * @param array{
     *     id: int,
     *     order_id: int,
     *     notification_type: int,
     *     created_at: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            orderId: (int)$row['order_id'],
            notificationType: NotificationType::from((int)$row['notification_type']),
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
            'id'                => $this->id,
            'notification_type' => ['id' => $this->notificationType->value, 'label' => $this->notificationType->getLabel()],
            'created_at'        => $this->createdAt,
        ];
    }
}
