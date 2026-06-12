<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderSection\FindByOrderId;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Order\ReadModel\OrderSection\Interface\OrderSectionModelInterface;
use App\Modules\Order\ReadModel\OrderSection\OrderSectionDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class OrderSectionFindByOrderIdFetcher
{
    private const string TABLE = 'order_sections';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of OrderSectionModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(OrderSectionFindByOrderIdQuery $query, string $modelClass = OrderSectionDetails::class): array
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
