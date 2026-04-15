<?php

declare(strict_types=1);

namespace App\Modules\Production\Query\Production\GetById;

use App\Components\Cacher\Cacher;
use App\Components\Exception\DomainExceptionModule;
use App\Modules\Production\ReadModel\Production\ProductionById;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProductionGetByIdFetcher
{
    private const string TABLE = 'productions';
    private const int CACHE_TTL = 900;

    public function __construct(
        private Connection $connection,
        private Cacher $cacher,
    ) {}

    /**
     * @throws Exception
     */
    public function fetch(ProductionGetByIdQuery $query): ProductionById
    {
        $key = 'production_by_id_' . $query->id;

        /** @var ProductionById|null $cached */
        $cached = $this->cacher->get($key);

        if ($cached !== null) {
            return $cached;
        }

        $row = $this->connection->createQueryBuilder()
            ->select('id', 'name')
            ->from(self::TABLE)
            ->where('id = :id')
            ->setParameter('id', $query->id)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            throw new DomainExceptionModule(
                module: 'production',
                message: 'error.production_not_found',
                code: 1
            );
        }

        /** @var array{id: int, name: string} $row */
        $result = ProductionById::fromRow($row);
        $this->cacher->set($key, $result, self::CACHE_TTL);

        return $result;
    }
}
