<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialPricingByArea\FindByMaterialIdAndOptionId;

use App\Components\Fetcher\FetcherCache;
use App\Components\Fetcher\FetcherCacheKey;
use App\Components\ReadModel\ReadModelFields;
use App\Modules\Material\ReadModel\MaterialPricingByArea\Interface\MaterialPricingByAreaModelInterface;
use App\Modules\Material\ReadModel\MaterialPricingByArea\MaterialPricingByAreaByMaterialIdAndOptionId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialPricingByAreaFindByMaterialIdAndOptionIdFetcher
{
    private const string TABLE = 'material_pricing_by_area';
    private const int CACHE_TTL = 900;
    public const string CACHE_TAG = 'material_pricing_by_area.by_material_id_and_option_id';

    public function __construct(
        private Connection $connection,
        private FetcherCache $fetcherCache,
    ) {}

    /**
     * @template T of MaterialPricingByAreaModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(
        MaterialPricingByAreaFindByMaterialIdAndOptionIdQuery $query,
        string $modelClass = MaterialPricingByAreaByMaterialIdAndOptionId::class,
    ): array
    {
        $tag = FetcherCacheKey::tag(self::CACHE_TAG, [$query->materialId, $query->optionId]);
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
            ->andWhere('option_id = :optionId')
            ->setParameter('materialId', $query->materialId)
            ->setParameter('optionId', $query->optionId)
            ->orderBy('id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $result = $modelClass::fromRows($rows);
        $this->fetcherCache->set($key, $result, self::CACHE_TTL, [$tag]);

        return $result;
    }
}
