<?php

declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Order\Command\OrderFile\Remove\RemoveOrderFileCommand;
use App\Modules\Order\Command\OrderFile\Remove\RemoveOrderFileHandler;
use App\Modules\Order\Command\OrderNotification\Delete\DeleteOrderNotificationCommand;
use App\Modules\Order\Command\OrderNotification\Delete\DeleteOrderNotificationHandler;
use App\Modules\Order\Entity\OrderFile\OrderFileRepository;
use App\Modules\Order\Entity\OrderNotification\OrderNotificationRepository;

final readonly class OrderStructureDeleteService
{
    public function __construct(
        private OrderStructureSyncerService $structureSyncerService,
        private OrderFileRepository $orderFileRepository,
        private RemoveOrderFileHandler $removeFileHandler,
        private OrderNotificationRepository $orderNotificationRepository,
        private DeleteOrderNotificationHandler $deleteNotificationHandler,
    ) {}

    public function delete(int $orderId): void
    {
        foreach ($this->orderFileRepository->findByOrderId($orderId) as $file) {
            $this->removeFileHandler->handle(new RemoveOrderFileCommand((int)$file->id));
        }

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
            $this->deleteNotificationHandler->handle(new DeleteOrderNotificationCommand((int)$notification->id));
        }
    }
}
