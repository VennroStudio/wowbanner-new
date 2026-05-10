<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderService\Create;

use App\Modules\Order\Entity\OrderService\Fields\Enums\ServiceType;
use App\Modules\Order\Entity\OrderService\OrderService;
use App\Modules\Order\Entity\OrderService\OrderServiceRepository;

final readonly class CreateOrderServiceHandler
{
    public function __construct(
        private OrderServiceRepository $repository,
    ) {}

    public function handle(CreateOrderServiceCommand $command): void
    {
        $orderService = OrderService::create(
            orderId: $command->orderId,
            serviceType: ServiceType::from($command->serviceType),
            price: $command->price,
            note: $command->note,
        );

        $this->repository->add($orderService);
    }
}
