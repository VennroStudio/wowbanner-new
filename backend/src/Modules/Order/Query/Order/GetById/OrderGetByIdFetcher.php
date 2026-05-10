<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\Order\GetById;

use App\Components\Cacher\Cacher;
use App\Components\Exception\DomainExceptionModule;
use App\Modules\Order\ReadModel\Order\OrderById;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class OrderGetByIdFetcher
{
    private const string TABLE = 'orders';
    private const int CACHE_TTL = 900;

    public function __construct(
        private Connection $connection,
        private Cacher $cacher,
    ) {}

    /**
     * @throws Exception
     */
    public function fetch(OrderGetByIdQuery $query): OrderById
    {
        $key = 'order_by_id_' . $query->id;

        /** @var OrderById|null $cached */
        $cached = $this->cacher->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $row = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'creator_id',
                'manager_id',
                'designer_id',
                'client_id',
                'status_type',
                'storage_type',
                'general_note',
                'extension',
                'created_at',
                'accepted_at',
                'deadline_at'
            )
            ->from(self::TABLE)
            ->where('id = :id')
            ->setParameter('id', $query->id)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            throw new DomainExceptionModule(
                module: 'order',
                message: 'error.order_not_found',
                code: 1
            );
        }

        /**
         * @var array{
         *     id: int,
         *     creator_id: int,
         *     manager_id: int|null,
         *     designer_id: int|null,
         *     client_id: int,
         *     status_type: int,
         *     storage_type: int,
         *     general_note: string|null,
         *     extension: string|null,
         *     created_at: string,
         *     accepted_at: string,
         *     deadline_at: string
         * } $row
         */
        $result = OrderById::fromRow($row);
        $this->cacher->set($key, $result, self::CACHE_TTL);

        return $result;
    }
}
