<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialOption\FindByMaterialId;

use App\Components\Fetcher\FetcherCache;
use App\Components\Fetcher\FetcherCacheKey;
use App\Components\ReadModel\ReadModelFields;
use App\Modules\Material\ReadModel\MaterialOption\Interface\MaterialOptionModelInterface;
use App\Modules\Material\ReadModel\MaterialOption\MaterialOptionDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialOptionFindByMaterialIdFetcher
{
    private const string TABLE = 'material_options';
    private const int CACHE_TTL = 900;
    public const string CACHE_TAG = 'material_option.by_material_id';

    public function __construct(
        private Connection $connection,
        private FetcherCache $fetcherCache,
    ) {}

    /**
     * @template T of MaterialOptionModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(
        MaterialOptionFindByMaterialIdQuery $query,
        string $modelClass = MaterialOptionDetails::class,
    ): array
    {
        $tag = FetcherCacheKey::tag(self::CACHE_TAG, [$query->materialId]);
        $key = FetcherCacheKey::key($tag, $modelClass);

        /** @var list<T>|null $cached */
        $cached = $this->fetcherCache->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $rows = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select($modelClass::fields()))
            ->from(self::TABLE)
            ->where('material_id = :materialId')
            ->setParameter('materialId', $query->materialId)
            ->orderBy('id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $result = $modelClass::fromRows($rows);
        $this->fetcherCache->set($key, $result, self::CACHE_TTL, [$tag]);

        return $result;
    }
}
