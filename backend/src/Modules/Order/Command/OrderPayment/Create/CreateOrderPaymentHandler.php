<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderPayment\Create;

use App\Modules\Order\Entity\OrderPayment\Fields\Enums\OperationType;
use App\Modules\Order\Entity\OrderPayment\Fields\Enums\PaymentType;
use App\Modules\Order\Entity\OrderPayment\OrderPayment;
use App\Modules\Order\Entity\OrderPayment\OrderPaymentRepository;

final readonly class CreateOrderPaymentHandler
{
    public function __construct(
        private OrderPaymentRepository $repository,
    ) {}

    public function handle(CreateOrderPaymentCommand $command): void
    {
        $orderPayment = OrderPayment::create(
            orderId: $command->orderId,
            clientId: $command->clientId,
            operationType: OperationType::from($command->operationType),
            paymentType: PaymentType::from($command->paymentType),
            reason: $command->reason,
            note: $command->note,
            confirmation: $command->confirmation,
        );

        $this->repository->add($orderPayment);
    }
}
