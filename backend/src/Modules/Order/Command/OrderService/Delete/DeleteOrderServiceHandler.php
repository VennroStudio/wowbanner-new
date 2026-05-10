<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderService\Delete;

use App\Modules\Order\Entity\OrderService\OrderServiceRepository;

final readonly class DeleteOrderServiceHandler
{
    public function __construct(
        private OrderServiceRepository $repository,
    ) {}

    public function handle(DeleteOrderServiceCommand $command): void
    {
        $orderService = $this->repository->getById($command->id);

        $this->repository->remove($orderService);
    }
}
