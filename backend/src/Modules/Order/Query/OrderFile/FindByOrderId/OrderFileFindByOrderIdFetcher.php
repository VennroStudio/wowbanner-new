<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderFile\FindByOrderId;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Order\ReadModel\OrderFile\Interface\OrderFileModelInterface;
use App\Modules\Order\ReadModel\OrderFile\OrderFileDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class OrderFileFindByOrderIdFetcher
{
    private const string TABLE = 'order_files';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of OrderFileModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(OrderFileFindByOrderIdQuery $query, string $modelClass = OrderFileDetails::class): array
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
