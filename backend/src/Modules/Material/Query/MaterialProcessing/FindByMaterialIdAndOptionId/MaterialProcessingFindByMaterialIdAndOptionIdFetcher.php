<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialProcessing\FindByMaterialIdAndOptionId;

use App\Components\Fetcher\FetcherCache;
use App\Components\Fetcher\FetcherCacheKey;
use App\Components\ReadModel\ReadModelFields;
use App\Modules\Material\ReadModel\MaterialProcessing\Interface\MaterialProcessingModelInterface;
use App\Modules\Material\ReadModel\MaterialProcessing\MaterialProcessingByMaterialIdAndOptionId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialProcessingFindByMaterialIdAndOptionIdFetcher
{
    private const string TABLE = 'material_processings';
    private const int CACHE_TTL = 900;
    public const string CACHE_TAG = 'material_processing.by_material_id_and_option_id';

    public function __construct(
        private Connection $connection,
        private FetcherCache $fetcherCache,
    ) {}

    /**
     * @template T of MaterialProcessingModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(
        MaterialProcessingFindByMaterialIdAndOptionIdQuery $query,
        string $modelClass = MaterialProcessingByMaterialIdAndOptionId::class,
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
            ->select(...ReadModelFields::select($modelClass::fields(), 'mp'))
            ->from(self::TABLE, 'mp')
            ->leftJoin('mp', 'processings', 'p', 'p.id = mp.processing_id')
            ->where('mp.material_id = :materialId')
            ->andWhere('mp.option_id = :optionId')
            ->setParameter('materialId', $query->materialId)
            ->setParameter('optionId', $query->optionId)
            ->orderBy('mp.id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $result = $modelClass::fromRows($rows);
        $this->fetcherCache->set($key, $result, self::CACHE_TTL, [$tag]);

        return $result;
    }
}
