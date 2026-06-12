<?php

declare(strict_types=1);

namespace App\Http\Unifier\Order;

use App\Components\Http\Unifier\UnifierHelper;
use App\Components\Http\Unifier\UnifierInterface;
use App\Http\Unifier\Client\ClientUnifier;
use App\Modules\Client\Query\Client\GetById\ClientGetByIdFetcher;
use App\Modules\Client\Query\Client\GetById\ClientGetByIdQuery;
use App\Modules\Order\Query\OrderDelivery\FindByOrderId\OrderDeliveryFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderDelivery\FindByOrderId\OrderDeliveryFindByOrderIdQuery;
use App\Modules\Order\Query\OrderFile\FindByOrderId\OrderFileFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderFile\FindByOrderId\OrderFileFindByOrderIdQuery;
use App\Modules\Order\Query\OrderItem\FindByOrderId\OrderItemFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderItem\FindByOrderId\OrderItemFindByOrderIdQuery;
use App\Modules\Order\Query\OrderItemMilling\FindByOrderId\OrderItemMillingFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderItemMilling\FindByOrderId\OrderItemMillingFindByOrderIdQuery;
use App\Modules\Order\Query\OrderNotification\FindByOrderId\OrderNotificationFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderNotification\FindByOrderId\OrderNotificationFindByOrderIdQuery;
use App\Modules\Order\Query\OrderPayment\FindByOrderId\OrderPaymentFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderPayment\FindByOrderId\OrderPaymentFindByOrderIdQuery;
use App\Modules\Order\Query\OrderSection\FindByOrderId\OrderSectionFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderSection\FindByOrderId\OrderSectionFindByOrderIdQuery;
use App\Modules\Order\Query\OrderService\FindByOrderId\OrderServiceFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderService\FindByOrderId\OrderServiceFindByOrderIdQuery;
use App\Modules\Order\ReadModel\Order\Interface\OrderModelInterface;
use App\Modules\Order\ReadModel\OrderService\OrderServiceByOrderId;
use App\Modules\User\Query\User\GetById\UserGetByIdFetcher;
use App\Modules\User\Query\User\GetById\UserGetByIdQuery;
use Doctrine\DBAL\Exception;
use Override;

final readonly class OrderUnifier implements UnifierInterface
{
    public function __construct(
        private ClientGetByIdFetcher $clientFetcher,
        private ClientUnifier $clientUnifier,
        private UserGetByIdFetcher $userFetcher,
        private OrderDeliveryFindByOrderIdFetcher $deliveryFetcher,
        private OrderDeliveryUnifier $deliveryUnifier,
        private OrderFileFindByOrderIdFetcher $fileFetcher,
        private OrderFileUnifier $fileUnifier,
        private OrderItemFindByOrderIdFetcher $itemFetcher,
        private OrderItemUnifier $itemUnifier,
        private OrderItemMillingFindByOrderIdFetcher $itemMillingFetcher,
        private OrderItemMillingUnifier $itemMillingUnifier,
        private OrderPaymentFindByOrderIdFetcher $paymentFetcher,
        private OrderPaymentUnifier $paymentUnifier,
        private OrderSectionFindByOrderIdFetcher $sectionFetcher,
        private OrderSectionUnifier $sectionUnifier,
        private OrderServiceFindByOrderIdFetcher $serviceFetcher,
        private OrderServiceUnifier $serviceUnifier,
        private OrderNotificationFindByOrderIdFetcher $notificationFetcher,
        private OrderNotificationUnifier $notificationUnifier,
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
        $clientId = $item->getClientId();
        $managerId = $item->getManagerId();
        $designerId = $item->getDesignerId();

        $delivery = $this->deliveryFetcher->fetch(new OrderDeliveryFindByOrderIdQuery($orderId));
        $files = $this->fileFetcher->fetch(new OrderFileFindByOrderIdQuery($orderId));
        $items = $this->itemFetcher->fetch(new OrderItemFindByOrderIdQuery($orderId));
        $millings = $this->itemMillingFetcher->fetch(new OrderItemMillingFindByOrderIdQuery($orderId));
        $payments = $this->paymentFetcher->fetch(new OrderPaymentFindByOrderIdQuery($orderId));
        $sections = $this->sectionFetcher->fetch(new OrderSectionFindByOrderIdQuery($orderId));
        $services = $this->serviceFetcher->fetch(new OrderServiceFindByOrderIdQuery($orderId));
        $notifications = $this->notificationFetcher->fetch(new OrderNotificationFindByOrderIdQuery($orderId));
        $client = $this->clientFetcher->fetch(new ClientGetByIdQuery($clientId));
        $manager = $managerId !== null ? $this->userFetcher->fetch(new UserGetByIdQuery($managerId)) : null;
        $designer = $designerId !== null ? $this->userFetcher->fetch(new UserGetByIdQuery($designerId)) : null;

        $data['delivery'] = $delivery !== null
            ? $this->deliveryUnifier->unifyOne(null, $delivery)
            : null;
        $data['client'] = $this->clientUnifier->unifyOne(null, $client);
        $data['client']['name'] = $this->buildClientName($client);
        $data['manager'] = $manager !== null
            ? [
                'id' => $manager->id,
                'name' => $this->buildUserName($manager->firstName, $manager->lastName),
            ]
            : null;
        $data['designer'] = $designer !== null
            ? [
                'id' => $designer->id,
                'name' => $this->buildUserName($designer->firstName, $designer->lastName),
            ]
            : null;
        $data['files'] = $this->fileUnifier->unify(null, $files);
        $data['items'] = $this->itemUnifier->unify(null, $items);
        $data['millings'] = $this->itemMillingUnifier->unify(null, $millings);
        $data['payments'] = $this->paymentUnifier->unify(null, $payments);
        $data['sections'] = $this->sectionUnifier->unify(null, $sections);
        $data['services'] = $this->serviceUnifier->unify(null, $services);
        $data['notifications'] = $this->notificationUnifier->unify(null, $notifications);
        $data['price'] = $this->calculatePrice($services);

        return UnifierHelper::withTimestamps($data, $item);
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

    private function buildClientName(object $client): string
    {
        if ($client->oldFullName !== null && $client->oldFullName !== '') {
            return $client->oldFullName;
        }

        return trim(implode(' ', array_filter([
            $client->lastName,
            $client->firstName,
            $client->middleName,
        ])));
    }

    private function buildUserName(string $firstName, string $lastName): string
    {
        return trim($lastName . ' ' . $firstName);
    }
}
