<?php

declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Order\Entity\OrderNotification\OrderNotificationRepository;

final readonly class OrderStructureDeleteService
{
    public function __construct(
        private OrderStructureSyncerService $structureSyncerService,
        private OrderNotificationRepository $orderNotificationRepository,
    ) {}

    public function delete(int $orderId): void
    {
        $this->structureSyncerService->sync(
            orderId: $orderId,
            delivery: null,
            files: [],
            items: [],
            millings: [],
            payments: [],
            sections: [],
            services: [],
        );

        foreach ($this->orderNotificationRepository->findByOrderId($orderId) as $notification) {
            $this->orderNotificationRepository->remove($notification);
        }
    }
}
