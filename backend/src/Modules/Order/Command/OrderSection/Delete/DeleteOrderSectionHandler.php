<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderSection\Delete;

use App\Modules\Order\Entity\OrderSection\OrderSectionRepository;

final readonly class DeleteOrderSectionHandler
{
    public function __construct(
        private OrderSectionRepository $repository,
    ) {}

    public function handle(DeleteOrderSectionCommand $command): void
    {
        $orderSection = $this->repository->getById($command->id);

        $this->repository->remove($orderSection);
    }
}
