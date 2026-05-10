<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderDelivery\Delete;

use App\Modules\Order\Entity\OrderDelivery\OrderDeliveryRepository;

final readonly class DeleteOrderDeliveryHandler
{
    public function __construct(
        private OrderDeliveryRepository $repository,
    ) {}

    public function handle(DeleteOrderDeliveryCommand $command): void
    {
        $orderDelivery = $this->repository->getById($command->id);

        $this->repository->remove($orderDelivery);
    }
}
