<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderPayment\FindByOrderId;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Order\ReadModel\OrderPayment\Interface\OrderPaymentModelInterface;
use App\Modules\Order\ReadModel\OrderPayment\OrderPaymentDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class OrderPaymentFindByOrderIdFetcher
{
    private const string TABLE = 'order_payments';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of OrderPaymentModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(OrderPaymentFindByOrderIdQuery $query, string $modelClass = OrderPaymentDetails::class): array
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
