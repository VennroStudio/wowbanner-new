<?php

declare(strict_types=1);

namespace App\Modules\Product\Query\Product\GetById;

use App\Components\Cacher\Cacher;
use App\Components\Exception\DomainExceptionModule;
use App\Modules\Product\ReadModel\Product\ProductById;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProductGetByIdFetcher
{
    private const string TABLE = 'products';
    private const int CACHE_TTL = 900;

    public function __construct(
        private Connection $connection,
        private Cacher $cacher,
    ) {}

    /**
     * @throws Exception
     */
    public function fetch(ProductGetByIdQuery $query): ProductById
    {
        $key = 'Product_by_id_' . $query->id;

        /** @var ProductById|null $cached */
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
                module: 'Product',
                message: 'error.Product_not_found',
                code: 1
            );
        }

        /** @var array{id: int, name: string} $row */
        $result = ProductById::fromRow($row);
        $this->cacher->set($key, $result, self::CACHE_TTL);

        return $result;
    }
}
