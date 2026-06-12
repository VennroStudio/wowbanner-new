<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderItem\FindByOrderId;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Order\ReadModel\OrderItem\Interface\OrderItemModelInterface;
use App\Modules\Order\ReadModel\OrderItem\OrderItemDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

final readonly class OrderItemFindByOrderIdFetcher
{
    public const string ALIAS = 'oi';
    private const string TABLE = 'order_items';

    public function __construct(
        private Connection $connection,
    ) {}

    public function joinForFilter(QueryBuilder $qb, string $alias): void
    {
        $qb->leftJoin($alias, self::TABLE, self::ALIAS, self::ALIAS . '.order_id = ' . $alias . '.id');
    }

    /**
     * @template T of OrderItemModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(OrderItemFindByOrderIdQuery $query, string $modelClass = OrderItemDetails::class): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select($modelClass::fields()))
            ->from(self::TABLE)
            ->where('order_id = :orderId')
            ->setParameter('orderId', $query->orderId)
            ->executeQuery()
            ->fetchAllAssociative();

        return $modelClass::fromRows($rows);
    }
}
