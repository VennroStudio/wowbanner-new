<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderNotification\Delete;

use App\Modules\Order\Entity\OrderNotification\OrderNotificationRepository;

final readonly class DeleteOrderNotificationHandler
{
    public function __construct(
        private OrderNotificationRepository $repository,
    ) {}

    public function handle(DeleteOrderNotificationCommand $command): void
    {
        $orderNotification = $this->repository->getById($command->id);

        $this->repository->remove($orderNotification);
    }
}
