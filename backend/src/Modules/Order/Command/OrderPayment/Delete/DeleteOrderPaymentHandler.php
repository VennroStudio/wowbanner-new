<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderPayment\Delete;

use App\Modules\Order\Entity\OrderPayment\OrderPaymentRepository;

final readonly class DeleteOrderPaymentHandler
{
    public function __construct(
        private OrderPaymentRepository $repository,
    ) {}

    public function handle(DeleteOrderPaymentCommand $command): void
    {
        $orderPayment = $this->repository->getById($command->id);

        $this->repository->remove($orderPayment);
    }
}
