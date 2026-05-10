<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderSection\FindByOrderId;

use App\Modules\Order\ReadModel\OrderSection\OrderSectionByOrderId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class OrderSectionFindByOrderIdFetcher
{
    private const string TABLE = 'order_sections';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return list<OrderSectionByOrderId>
     * @throws Exception
     */
    public function fetch(OrderSectionFindByOrderIdQuery $query): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'order_id', 'section_type')
            ->from(self::TABLE)
            ->where('order_id = :orderId')
            ->setParameter('orderId', $query->orderId)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<OrderSectionByOrderId> $items */
        $items = OrderSectionByOrderId::fromRows($rows);

        return $items;
    }
}
