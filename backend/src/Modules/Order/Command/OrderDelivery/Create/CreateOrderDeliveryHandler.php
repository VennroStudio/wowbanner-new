<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderDelivery\Create;

use App\Modules\Order\Entity\OrderDelivery\Fields\Enums\DeliveryType;
use App\Modules\Order\Entity\OrderDelivery\OrderDelivery;
use App\Modules\Order\Entity\OrderDelivery\OrderDeliveryRepository;

final readonly class CreateOrderDeliveryHandler
{
    public function __construct(
        private OrderDeliveryRepository $repository,
    ) {}

    public function handle(CreateOrderDeliveryCommand $command): void
    {
        $orderDelivery = OrderDelivery::create(
            orderId: $command->orderId,
            deliveryType: DeliveryType::from($command->deliveryType),
            address: $command->address,
            comment: $command->comment,
        );

        $this->repository->add($orderDelivery);
    }
}
