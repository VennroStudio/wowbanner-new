<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\Order\GetById;

use App\Components\Cacher\CacheKey;
use App\Components\Cacher\Cacher;
use App\Components\Exception\DomainExceptionModule;
use App\Components\ReadModel\ReadModelFields;
use App\Modules\Order\ReadModel\Order\Interface\OrderModelInterface;
use App\Modules\Order\ReadModel\Order\OrderDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class OrderGetByIdFetcher
{
    public const string CACHE_TAG = 'order_by_id';
    private const string TABLE = 'orders';
    private const int CACHE_TTL = 900;

    public function __construct(
        private Connection $connection,
        private Cacher $cacher,
    ) {}

    /**
     * @template T of OrderModelInterface
     * @param class-string<T> $modelClass
     * @return T
     * @throws Exception
     */
    public function fetch(OrderGetByIdQuery $query, string $modelClass = OrderDetails::class): OrderModelInterface
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
                module: 'order',
                message: 'error.order_not_found',
                code: 1
            );
        }

        $result = $modelClass::fromRow($row);
        $this->cacher->setTagged($key, $result, self::CACHE_TTL, [$tag]);

        return $result;
    }
}
