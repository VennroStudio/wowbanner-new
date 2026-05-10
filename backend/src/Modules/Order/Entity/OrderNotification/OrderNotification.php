<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderNotification;

use App\Components\Clock\UtcClock;
use App\Modules\Order\Entity\OrderNotification\Fields\Enums\NotificationType;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_notifications')]
#[ORM\Index(name: 'idx_order_notification_order_id', columns: ['order_id'])]
#[ORM\Index(name: 'idx_order_notification_type', columns: ['notification_type'])]
#[ORM\Index(name: 'idx_order_notification_created_at', columns: ['created_at'])]
class OrderNotification
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $orderId;

    #[ORM\Column(type: Types::INTEGER, enumType: NotificationType::class)]
    private(set) NotificationType $notificationType;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private(set) DateTimeImmutable $createdAt;

    private function __construct(
        int $orderId,
        NotificationType $notificationType,
    ) {
        $this->orderId = $orderId;
        $this->notificationType = $notificationType;
        $this->createdAt = UtcClock::now();
    }

    public static function create(
        int $orderId,
        NotificationType $notificationType,
    ): self {
        return new self(
            orderId: $orderId,
            notificationType: $notificationType,
        );
    }

    public function edit(NotificationType $notificationType): void
    {
        $this->notificationType = $notificationType;
    }
}
