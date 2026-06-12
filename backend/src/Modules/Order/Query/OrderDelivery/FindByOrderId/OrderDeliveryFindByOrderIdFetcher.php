<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderDelivery\FindByOrderId;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Order\ReadModel\OrderDelivery\Interface\OrderDeliveryModelInterface;
use App\Modules\Order\ReadModel\OrderDelivery\OrderDeliveryDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class OrderDeliveryFindByOrderIdFetcher
{
    private const string TABLE = 'order_deliveries';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of OrderDeliveryModelInterface
     * @param class-string<T> $modelClass
     * @return T|null
     * @throws Exception
     */
    public function fetch(
        OrderDeliveryFindByOrderIdQuery $query,
        string $modelClass = OrderDeliveryDetails::class,
    ): ?OrderDeliveryModelInterface {
        $row = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select($modelClass::fields()))
            ->from(self::TABLE)
            ->where('order_id = :orderId')
            ->setParameter('orderId', $query->orderId)
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            return null;
        }

        return $modelClass::fromRow($row);
    }
}
