<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\Material\GetById;

use App\Components\Exception\DomainExceptionModule;
use App\Components\Cacher\CacheKey;
use App\Components\Cacher\Cacher;
use App\Components\ReadModel\ReadModelFields;
use App\Modules\Material\ReadModel\Material\Interface\MaterialModelInterface;
use App\Modules\Material\ReadModel\Material\MaterialDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialGetByIdFetcher
{
    private const string TABLE = 'materials';
    private const int CACHE_TTL = 900;
    public const string CACHE_TAG = 'material_by_id';

    public function __construct(
        private Connection $connection,
        private Cacher $cacher,
    ) {}

    /**
     * @template T of MaterialModelInterface
     * @param class-string<T> $modelClass
     * @return T
     * @throws Exception
     */
    public function fetch(MaterialGetByIdQuery $query, string $modelClass = MaterialDetails::class): MaterialModelInterface
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
                module: 'material',
                message: 'error.material_not_found',
                code: 1
            );
        }

        $result = $modelClass::fromRow($row);
        $this->cacher->setTagged($key, $result, self::CACHE_TTL, [$tag]);

        return $result;
    }
}
