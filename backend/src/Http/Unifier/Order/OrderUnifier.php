<?php

declare(strict_types=1);

namespace App\Http\Unifier\Order;

use App\Components\Http\Unifier\UnifierHelper;
use App\Components\Http\Unifier\UnifierInterface;
use App\Modules\Order\Query\OrderDelivery\FindByOrderId\OrderDeliveryFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderDelivery\FindByOrderId\OrderDeliveryFindByOrderIdQuery;
use App\Modules\Order\Query\OrderFile\FindByOrderId\OrderFileFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderFile\FindByOrderId\OrderFileFindByOrderIdQuery;
use App\Modules\Order\Query\OrderItem\FindByOrderId\OrderItemFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderItem\FindByOrderId\OrderItemFindByOrderIdQuery;
use App\Modules\Order\Query\OrderItemMilling\FindByOrderId\OrderItemMillingFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderItemMilling\FindByOrderId\OrderItemMillingFindByOrderIdQuery;
use App\Modules\Order\Query\OrderItemProcessing\FindByItemId\OrderItemProcessingFindByItemIdFetcher;
use App\Modules\Order\Query\OrderItemProcessing\FindByItemId\OrderItemProcessingFindByItemIdQuery;
use App\Modules\Order\Query\OrderNotification\FindByOrderId\OrderNotificationFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderNotification\FindByOrderId\OrderNotificationFindByOrderIdQuery;
use App\Modules\Order\Query\OrderPayment\FindByOrderId\OrderPaymentFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderPayment\FindByOrderId\OrderPaymentFindByOrderIdQuery;
use App\Modules\Order\Query\OrderSection\FindByOrderId\OrderSectionFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderSection\FindByOrderId\OrderSectionFindByOrderIdQuery;
use App\Modules\Order\Query\OrderService\FindByOrderId\OrderServiceFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderService\FindByOrderId\OrderServiceFindByOrderIdQuery;
use App\Modules\Order\ReadModel\Order\Interface\OrderModelInterface;
use App\Modules\Order\ReadModel\OrderDelivery\OrderDeliveryByOrderId;
use App\Modules\Order\ReadModel\OrderFile\OrderFileByOrderId;
use App\Modules\Order\ReadModel\OrderItem\OrderItemByOrderId;
use App\Modules\Order\ReadModel\OrderItemMilling\OrderItemMillingByOrderId;
use App\Modules\Order\ReadModel\OrderItemProcessing\OrderItemProcessingByOrderId;
use App\Modules\Order\ReadModel\OrderNotification\OrderNotificationByOrderId;
use App\Modules\Order\ReadModel\OrderPayment\OrderPaymentByOrderId;
use App\Modules\Order\ReadModel\OrderSection\OrderSectionByOrderId;
use App\Modules\Order\ReadModel\OrderService\OrderServiceByOrderId;
use Doctrine\DBAL\Exception;
use Override;

final readonly class OrderUnifier implements UnifierInterface
{
    public function __construct(
        private OrderDeliveryFindByOrderIdFetcher $deliveryFetcher,
        private OrderFileFindByOrderIdFetcher $fileFetcher,
        private OrderItemFindByOrderIdFetcher $itemFetcher,
        private OrderItemProcessingFindByItemIdFetcher $itemProcessingFetcher,
        private OrderItemMillingFindByOrderIdFetcher $itemMillingFetcher,
        private OrderPaymentFindByOrderIdFetcher $paymentFetcher,
        private OrderSectionFindByOrderIdFetcher $sectionFetcher,
        private OrderServiceFindByOrderIdFetcher $serviceFetcher,
        private OrderNotificationFindByOrderIdFetcher $notificationFetcher,
    ) {}

    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if ($item === null) {
            return [];
        }

        return $this->unify($userId, [$item])[0] ?? [];
    }

    /**
     * @param list<OrderModelInterface> $items
     * @return list<array<string, mixed>>
     * @throws Exception
     */
    #[Override]
    public function unify(?int $userId, array $items): array
    {
        if ($items === []) {
            return [];
        }

        return array_map(
            fn(OrderModelInterface $item): array => $this->map($item),
            $items,
        );
    }

    /**
     * @param OrderModelInterface $item
     * @return array<string, mixed>
     * @throws Exception
     */
    #[Override]
    public function map(object $item): array
    {
        /** @var OrderModelInterface $item */
        $data = $item->toArray();
        $orderId = $item->getId();

        $delivery = $this->deliveryFetcher->fetch(new OrderDeliveryFindByOrderIdQuery($orderId));
        $files = $this->fileFetcher->fetch(new OrderFileFindByOrderIdQuery($orderId));
        $items = $this->itemFetcher->fetch(new OrderItemFindByOrderIdQuery($orderId));
        $millings = $this->itemMillingFetcher->fetch(new OrderItemMillingFindByOrderIdQuery($orderId));
        $payments = $this->paymentFetcher->fetch(new OrderPaymentFindByOrderIdQuery($orderId));
        $sections = $this->sectionFetcher->fetch(new OrderSectionFindByOrderIdQuery($orderId));
        $services = $this->serviceFetcher->fetch(new OrderServiceFindByOrderIdQuery($orderId));
        $notifications = $this->notificationFetcher->fetch(new OrderNotificationFindByOrderIdQuery($orderId));

        $data['delivery'] = $delivery !== null
            ? UnifierHelper::toArrayWithout($delivery, 'order_id')
            : null;
        $data['files'] = $this->mapFiles($files);
        $data['items'] = $this->mapItems($items);
        $data['millings'] = $this->mapMillings($millings);
        $data['payments'] = $this->mapPayments($payments);
        $data['sections'] = $this->mapSections($sections);
        $data['services'] = $this->mapServices($services);
        $data['notifications'] = $this->mapNotifications($notifications);
        $data['price'] = $this->calculatePrice($services);

        return UnifierHelper::withTimestamps($data, $item);
    }

    /**
     * @param list<OrderItemByOrderId> $items
     * @return list<array<string, mixed>>
     * @throws Exception
     */
    private function mapItems(array $items): array
    {
        return array_map(function (OrderItemByOrderId $item): array {
            $data = UnifierHelper::toArrayWithout($item, 'order_id');
            $processings = $this->itemProcessingFetcher->fetch(new OrderItemProcessingFindByItemIdQuery($item->id));

            $data['processings'] = array_map(
                static fn(OrderItemProcessingByOrderId $processing): array => UnifierHelper::toArrayWithout(
                    $processing,
                    'order_item_id',
                ),
                $processings,
            );

            return $data;
        }, $items);
    }

    /**
     * @param list<OrderFileByOrderId> $items
     * @return list<array<string, mixed>>
     */
    private function mapFiles(array $items): array
    {
        return array_map(
            static fn(OrderFileByOrderId $item): array => UnifierHelper::toArrayWithout($item, 'order_id'),
            $items,
        );
    }

    /**
     * @param list<OrderItemMillingByOrderId> $items
     * @return list<array<string, mixed>>
     */
    private function mapMillings(array $items): array
    {
        return array_map(
            static fn(OrderItemMillingByOrderId $item): array => UnifierHelper::toArrayWithout($item, 'order_id'),
            $items,
        );
    }

    /**
     * @param list<OrderPaymentByOrderId> $items
     * @return list<array<string, mixed>>
     */
    private function mapPayments(array $items): array
    {
        return array_map(
            static fn(OrderPaymentByOrderId $item): array => UnifierHelper::toArrayWithout($item, 'order_id'),
            $items,
        );
    }

    /**
     * @param list<OrderSectionByOrderId> $items
     * @return list<array<string, mixed>>
     */
    private function mapSections(array $items): array
    {
        return array_map(
            static fn(OrderSectionByOrderId $item): array => UnifierHelper::toArrayWithout($item, 'order_id'),
            $items,
        );
    }

    /**
     * @param list<OrderServiceByOrderId> $items
     * @return list<array<string, mixed>>
     */
    private function mapServices(array $items): array
    {
        return array_map(
            static fn(OrderServiceByOrderId $item): array => UnifierHelper::toArrayWithout($item, 'order_id'),
            $items,
        );
    }

    /**
     * @param list<OrderNotificationByOrderId> $items
     * @return list<array<string, mixed>>
     */
    private function mapNotifications(array $items): array
    {
        return array_map(
            static fn(OrderNotificationByOrderId $item): array => UnifierHelper::toArrayWithout($item, 'order_id'),
            $items,
        );
    }

    /**
     * @param list<OrderServiceByOrderId> $services
     */
    private function calculatePrice(array $services): string
    {
        $sum = array_reduce(
            $services,
            static fn(float $carry, OrderServiceByOrderId $service): float => $carry + (float) $service->price,
            0.0,
        );

        return number_format($sum, 2, '.', '');
    }
}
