<?php

declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Order\Command\OrderPayment\Create\CreateOrderPaymentCommand;
use App\Modules\Order\Command\OrderPayment\Create\CreateOrderPaymentHandler;
use App\Modules\Order\Command\OrderPayment\Update\UpdateOrderPaymentCommand;
use App\Modules\Order\Command\OrderPayment\Update\UpdateOrderPaymentHandler;
use App\Modules\Order\Entity\OrderPayment\OrderPaymentRepository;
use App\Modules\Order\ReadModel\OrderPayment\OrderPaymentItem;

final readonly class OrderPaymentSyncerService
{
    public function __construct(
        private OrderPaymentRepository $repository,
        private CreateOrderPaymentHandler $createHandler,
        private UpdateOrderPaymentHandler $updateHandler,
    ) {}

    /**
     * @param list<OrderPaymentItem> $items
     */
    public function sync(int $orderId, array $items): void
    {
        $currentItems = $this->repository->findByOrderId($orderId);
        $currentIds = array_map(static fn($item) => $item->id, $currentItems);
        $commandIds = array_filter(array_map(static fn(OrderPaymentItem $item) => $item->id, $items));

        foreach ($currentItems as $currentItem) {
            if (!in_array($currentItem->id, $commandIds, true)) {
                $this->repository->remove($currentItem);
            }
        }

        foreach ($items as $item) {
            if ($item->id !== null && in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(new UpdateOrderPaymentCommand(
                    id: $item->id,
                    clientId: $item->clientId,
                    operationType: $item->operationType,
                    paymentType: $item->paymentType,
                    reason: $item->reason,
                    note: $item->note,
                    confirmation: $item->confirmation,
                ));
                continue;
            }

            $this->createHandler->handle(new CreateOrderPaymentCommand(
                orderId: $orderId,
                clientId: $item->clientId,
                operationType: $item->operationType,
                paymentType: $item->paymentType,
                reason: $item->reason,
                note: $item->note,
                confirmation: $item->confirmation,
            ));
        }
    }
}
