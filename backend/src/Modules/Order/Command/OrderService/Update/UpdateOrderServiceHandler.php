<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderService\Update;

use App\Modules\Order\Entity\OrderService\Fields\Enums\ServiceType;
use App\Modules\Order\Entity\OrderService\OrderServiceRepository;

final readonly class UpdateOrderServiceHandler
{
    public function __construct(
        private OrderServiceRepository $repository,
    ) {}

    public function handle(UpdateOrderServiceCommand $command): void
    {
        $orderService = $this->repository->getById($command->id);

        $orderService->edit(
            serviceType: ServiceType::from($command->serviceType),
            price: $command->price,
            note: $command->note,
        );
    }
}
