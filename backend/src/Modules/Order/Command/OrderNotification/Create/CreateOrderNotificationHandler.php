<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderNotification\Create;

use App\Modules\Order\Entity\OrderNotification\Fields\Enums\NotificationType;
use App\Modules\Order\Entity\OrderNotification\OrderNotification;
use App\Modules\Order\Entity\OrderNotification\OrderNotificationRepository;

final readonly class CreateOrderNotificationHandler
{
    public function __construct(
        private OrderNotificationRepository $repository,
    ) {}

    public function handle(CreateOrderNotificationCommand $command): void
    {
        $orderNotification = OrderNotification::create(
            orderId: $command->orderId,
            notificationType: NotificationType::from($command->notificationType),
        );

        $this->repository->add($orderNotification);
    }
}
