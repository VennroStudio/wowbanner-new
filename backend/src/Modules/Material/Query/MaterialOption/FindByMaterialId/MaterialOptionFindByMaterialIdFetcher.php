<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialOption\FindByMaterialId;

use App\Components\Cacher\CacheKey;
use App\Components\Cacher\Cacher;
use App\Components\ReadModel\ReadModelFields;
use App\Modules\Material\ReadModel\MaterialOption\Interface\MaterialOptionModelInterface;
use App\Modules\Material\ReadModel\MaterialOption\MaterialOptionDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialOptionFindByMaterialIdFetcher
{
    private const string TABLE = 'material_options';
    private const int CACHE_TTL = 900;
    public const string CACHE_TAG = 'material_option_by_material_id';

    public function __construct(
        private Connection $connection,
        private Cacher $cacher,
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
        $tag = CacheKey::tag(self::CACHE_TAG, [$query->materialId]);
        $key = CacheKey::byClass($tag, $modelClass);

        /** @var list<T>|null $cached */
        $cached = $this->cacher->get($key);
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
        $this->cacher->setTagged($key, $result, self::CACHE_TTL, [$tag]);

        return $result;
    }
}
