<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderPayment\Update;

use App\Modules\Order\Entity\OrderPayment\Fields\Enums\OperationType;
use App\Modules\Order\Entity\OrderPayment\Fields\Enums\PaymentType;
use App\Modules\Order\Entity\OrderPayment\OrderPaymentRepository;

final readonly class UpdateOrderPaymentHandler
{
    public function __construct(
        private OrderPaymentRepository $repository,
    ) {}

    public function handle(UpdateOrderPaymentCommand $command): void
    {
        $orderPayment = $this->repository->getById($command->id);

        $orderPayment->edit(
            clientId: $command->clientId,
            operationType: OperationType::from($command->operationType),
            paymentType: PaymentType::from($command->paymentType),
            reason: $command->reason,
            note: $command->note,
            confirmation: $command->confirmation,
        );
    }
}
