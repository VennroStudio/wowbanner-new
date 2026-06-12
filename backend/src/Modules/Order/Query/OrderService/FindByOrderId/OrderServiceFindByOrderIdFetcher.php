<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderService\FindByOrderId;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Order\ReadModel\OrderService\Interface\OrderServiceModelInterface;
use App\Modules\Order\ReadModel\OrderService\OrderServiceDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

final readonly class OrderServiceFindByOrderIdFetcher
{
    public const string ALIAS = 'os';
    private const string TABLE = 'order_services';

    public function __construct(
        private Connection $connection,
    ) {}

    public function joinForFilter(QueryBuilder $qb, string $alias): void
    {
        $qb->leftJoin($alias, self::TABLE, self::ALIAS, self::ALIAS . '.order_id = ' . $alias . '.id');
    }

    /**
     * @template T of OrderServiceModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(OrderServiceFindByOrderIdQuery $query, string $modelClass = OrderServiceDetails::class): array
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
