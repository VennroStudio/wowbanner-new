<?php

declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Order\Command\OrderDelivery\Create\CreateOrderDeliveryCommand;
use App\Modules\Order\Command\OrderDelivery\Create\CreateOrderDeliveryHandler;
use App\Modules\Order\Command\OrderDelivery\Delete\DeleteOrderDeliveryCommand;
use App\Modules\Order\Command\OrderDelivery\Delete\DeleteOrderDeliveryHandler;
use App\Modules\Order\Command\OrderDelivery\Update\UpdateOrderDeliveryCommand;
use App\Modules\Order\Command\OrderDelivery\Update\UpdateOrderDeliveryHandler;
use App\Modules\Order\Entity\OrderDelivery\OrderDeliveryRepository;
use App\Modules\Order\ReadModel\OrderDelivery\OrderDeliveryItem;

final readonly class OrderDeliverySyncerService
{
    public function __construct(
        private OrderDeliveryRepository $repository,
        private CreateOrderDeliveryHandler $createHandler,
        private UpdateOrderDeliveryHandler $updateHandler,
        private DeleteOrderDeliveryHandler $deleteHandler,
    ) {}

    public function sync(int $orderId, ?OrderDeliveryItem $item): void
    {
        $currentItem = $this->repository->findByOrderId($orderId);

        if ($item === null) {
            if ($currentItem !== null) {
                $this->deleteHandler->handle(new DeleteOrderDeliveryCommand($currentItem->id));
            }

            return;
        }

        if ($currentItem !== null) {
            $this->updateHandler->handle(new UpdateOrderDeliveryCommand(
                id: $currentItem->id,
                deliveryType: $item->deliveryType,
                address: $item->address,
                comment: $item->comment,
            ));

            return;
        }

        $this->createHandler->handle(new CreateOrderDeliveryCommand(
            orderId: $orderId,
            deliveryType: $item->deliveryType,
            address: $item->address,
            comment: $item->comment,
        ));
    }
}
