<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderDelivery\Update;

use App\Modules\Order\Entity\OrderDelivery\Fields\Enums\DeliveryType;
use App\Modules\Order\Entity\OrderDelivery\OrderDeliveryRepository;

final readonly class UpdateOrderDeliveryHandler
{
    public function __construct(
        private OrderDeliveryRepository $repository,
    ) {}

    public function handle(UpdateOrderDeliveryCommand $command): void
    {
        $orderDelivery = $this->repository->getById($command->id);

        $orderDelivery->edit(
            deliveryType: DeliveryType::from($command->deliveryType),
            address: $command->address,
            comment: $command->comment,
        );
    }
}
