<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderItemMilling\FindByOrderId;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Order\ReadModel\OrderItemMilling\Interface\OrderItemMillingModelInterface;
use App\Modules\Order\ReadModel\OrderItemMilling\OrderItemMillingDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

final readonly class OrderItemMillingFindByOrderIdFetcher
{
    public const string ALIAS = 'oim';
    private const string TABLE = 'order_item_millings';

    public function __construct(
        private Connection $connection,
    ) {}

    public function joinForFilter(QueryBuilder $qb, string $alias): void
    {
        $qb->leftJoin($alias, self::TABLE, self::ALIAS, self::ALIAS . '.order_id = ' . $alias . '.id');
    }

    /**
     * @template T of OrderItemMillingModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(
        OrderItemMillingFindByOrderIdQuery $query,
        string $modelClass = OrderItemMillingDetails::class,
    ): array {
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
