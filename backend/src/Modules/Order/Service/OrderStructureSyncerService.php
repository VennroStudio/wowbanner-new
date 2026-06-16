<?php

declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Order\ReadModel\OrderDelivery\OrderDeliveryItem;
use App\Modules\Order\ReadModel\OrderFile\OrderFileItem;
use App\Modules\Order\ReadModel\OrderItem\OrderItemItem;
use App\Modules\Order\ReadModel\OrderItemMilling\OrderItemMillingItem;
use App\Modules\Order\ReadModel\OrderPayment\OrderPaymentItem;
use App\Modules\Order\ReadModel\OrderSection\OrderSectionItem;
use App\Modules\Order\ReadModel\OrderService\OrderServiceItem;

final readonly class OrderStructureSyncerService
{
    public function __construct(
        private OrderDeliverySyncerService $deliverySyncerService,
        private OrderFileSyncerService $fileSyncerService,
        private OrderItemSyncerService $itemSyncerService,
        private OrderItemMillingSyncerService $itemMillingSyncerService,
        private OrderPaymentSyncerService $paymentSyncerService,
        private OrderSectionSyncerService $sectionSyncerService,
        private OrderServiceSyncerService $serviceSyncerService,
    ) {}

    /**
     * @param list<OrderFileItem> $files
     * @param list<OrderItemItem> $items
     * @param list<OrderItemMillingItem> $millings
     * @param list<OrderPaymentItem> $payments
     * @param list<OrderSectionItem> $sections
     * @param list<OrderServiceItem> $services
     */
    public function sync(
        int $orderId,
        ?OrderDeliveryItem $delivery,
        array $files,
        array $items,
        array $millings,
        array $payments,
        array $sections,
        array $services,
    ): array {
        $this->deliverySyncerService->sync($orderId, $delivery);
        $this->fileSyncerService->sync($orderId, $files);
        $pendingProcessings = $this->itemSyncerService->sync($orderId, $items);
        $this->itemMillingSyncerService->sync($orderId, $millings);
        $this->paymentSyncerService->sync($orderId, $payments);
        $this->sectionSyncerService->sync($orderId, $sections);
        $this->serviceSyncerService->sync($orderId, $services);

        return $pendingProcessings;
    }

    public function syncItemProcessings(array $pendingProcessings): void
    {
        $this->itemSyncerService->syncProcessings($pendingProcessings);
    }
}
