<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderItemMilling\FindByOrderId;

use App\Modules\Order\ReadModel\OrderItemMilling\OrderItemMillingByOrderId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

final readonly class OrderItemMillingFindByOrderIdFetcher
{
    private const string TABLE = 'order_item_millings';
    public const string ALIAS = 'oim';

    public function __construct(
        private Connection $connection,
    ) {}

    public function joinForFilter(QueryBuilder $qb, string $alias): void
    {
        $qb->leftJoin($alias, self::TABLE, self::ALIAS, self::ALIAS . '.order_id = ' . $alias . '.id');
    }

    /**
     * @return list<OrderItemMillingByOrderId>
     * @throws Exception
     */
    public function fetch(OrderItemMillingFindByOrderIdQuery $query): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'order_id', 'source_item_id', 'print_id', 'material', 'performer_id', 'note', 'printed', 'ready', 'price')
            ->from(self::TABLE)
            ->where('order_id = :orderId')
            ->setParameter('orderId', $query->orderId)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<OrderItemMillingByOrderId> $items */
        $items = OrderItemMillingByOrderId::fromRows($rows);

        return $items;
    }
}
