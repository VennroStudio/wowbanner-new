<?php

declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Order\Command\OrderNotification\Delete\DeleteOrderNotificationCommand;
use App\Modules\Order\Command\OrderNotification\Delete\DeleteOrderNotificationHandler;
use App\Modules\Order\Entity\OrderNotification\OrderNotificationRepository;

final readonly class OrderStructureDeleteService
{
    public function __construct(
        private OrderStructureSyncerService $structureSyncerService,
        private OrderNotificationRepository $orderNotificationRepository,
        private DeleteOrderNotificationHandler $deleteNotificationHandler,
    ) {}

    public function delete(int $orderId): void
    {
        $this->structureSyncerService->sync(
            orderId: $orderId,
            delivery: null,
            files: [],
            keepFileIds: [],
            items: [],
            millings: [],
            payments: [],
            sections: [],
            services: [],
        );

        foreach ($this->orderNotificationRepository->findByOrderId($orderId) as $notification) {
            $this->deleteNotificationHandler->handle(new DeleteOrderNotificationCommand((int)$notification->id));
        }
    }
}
