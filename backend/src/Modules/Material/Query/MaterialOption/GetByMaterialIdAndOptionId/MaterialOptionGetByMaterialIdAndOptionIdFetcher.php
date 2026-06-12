<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialOption\GetByMaterialIdAndOptionId;

use App\Components\Exception\DomainExceptionModule;
use App\Components\Cacher\CacheKey;
use App\Components\Cacher\Cacher;
use App\Components\ReadModel\ReadModelFields;
use App\Modules\Material\ReadModel\MaterialOption\Interface\MaterialOptionModelInterface;
use App\Modules\Material\ReadModel\MaterialOption\MaterialOptionDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialOptionGetByMaterialIdAndOptionIdFetcher
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
     * @return T
     * @throws Exception
     */
    public function fetch(
        MaterialOptionGetByMaterialIdAndOptionIdQuery $query,
        string $modelClass = MaterialOptionDetails::class,
    ): MaterialOptionModelInterface
    {
        $tag = CacheKey::tag(self::CACHE_TAG, [$query->materialId, 'option_id', $query->optionId]);
        $key = CacheKey::byClass($tag, $modelClass);
        $tags = [
            $tag,
            CacheKey::tag('material_option_by_id', [$query->optionId]),
            CacheKey::tag('material_option_by_material_id', [$query->materialId]),
        ];

        /** @var T|null $cached */
        $cached = $this->cacher->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $row = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select($modelClass::fields()))
            ->from(self::TABLE)
            ->where('id = :optionId')
            ->andWhere('material_id = :materialId')
            ->setParameter('optionId', $query->optionId)
            ->setParameter('materialId', $query->materialId)
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
        $this->cacher->setTagged($key, $result, self::CACHE_TTL, $tags);

        return $result;
    }
}
