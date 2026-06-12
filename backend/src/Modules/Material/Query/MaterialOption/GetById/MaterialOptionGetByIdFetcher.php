<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialOption\GetById;

use App\Components\Exception\DomainExceptionModule;
use App\Components\Fetcher\FetcherCache;
use App\Components\Fetcher\FetcherCacheKey;
use App\Components\ReadModel\ReadModelFields;
use App\Modules\Material\ReadModel\MaterialOption\Interface\MaterialOptionModelInterface;
use App\Modules\Material\ReadModel\MaterialOption\MaterialOptionDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialOptionGetByIdFetcher
{
    private const string TABLE = 'material_options';
    private const int CACHE_TTL = 900;
    public const string CACHE_TAG = 'material_option.by_id';

    public function __construct(
        private Connection $connection,
        private FetcherCache $fetcherCache,
    ) {}

    /**
     * @template T of MaterialOptionModelInterface
     * @param class-string<T> $modelClass
     * @return T
     * @throws Exception
     */
    public function fetch(
        MaterialOptionGetByIdQuery $query,
        string $modelClass = MaterialOptionDetails::class,
    ): MaterialOptionModelInterface
    {
        $tag = FetcherCacheKey::tag(self::CACHE_TAG, [$query->id]);
        $key = FetcherCacheKey::key($tag, $modelClass);

        /** @var T|null $cached */
        $cached = $this->fetcherCache->get($key);

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
                module: 'material',
                message: 'error.material_option_not_found',
                code: 1
            );
        }

        $result = $modelClass::fromRow($row);
        $this->fetcherCache->set($key, $result, self::CACHE_TTL, [$tag]);

        return $result;
    }
}
