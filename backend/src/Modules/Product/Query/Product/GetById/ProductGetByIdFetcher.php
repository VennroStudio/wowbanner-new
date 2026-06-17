<?php

declare(strict_types=1);

namespace App\Modules\Product\Query\Product\GetById;

use App\Components\Cacher\CacheKey;
use App\Components\Cacher\Cacher;
use App\Components\Exception\DomainExceptionModule;
use App\Components\ReadModel\ReadModelFields;
use App\Modules\Product\ReadModel\Product\Interface\ProductModelInterface;
use App\Modules\Product\ReadModel\Product\ProductIdName;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProductGetByIdFetcher
{
    public const string CACHE_TAG = 'product_by_id';
    private const string TABLE = 'products';
    private const int CACHE_TTL = 900;

    public function __construct(
        private Connection $connection,
        private Cacher $cacher,
    ) {}

    /**
     * @template T of ProductModelInterface
     * @param class-string<T> $modelClass
     * @return T
     * @throws Exception
     */
    public function fetch(ProductGetByIdQuery $query, string $modelClass = ProductIdName::class): ProductModelInterface
    {
        $tag = CacheKey::tag(self::CACHE_TAG, [$query->id]);
        $key = CacheKey::byClass($tag, $modelClass);

        /** @var T|null $cached */
        $cached = $this->cacher->get($key);

        if ($cached !== null) {
            return $cached;
        }

        $row = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select($modelClass::fields()))
            ->from(self::TABLE)
            ->where('id = :id')
            ->setParameter('id', $query->id)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            throw new DomainExceptionModule(
                module: 'product',
                message: 'error.product_not_found',
                code: 1
            );
        }

        $result = $modelClass::fromRow($row);
        $this->cacher->setTagged($key, $result, self::CACHE_TTL, [$tag]);

        return $result;
    }
}
