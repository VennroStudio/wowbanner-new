<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\Material\GetById;

use App\Components\Cacher\Cacher;
use App\Components\Exception\DomainExceptionModule;
use App\Modules\Material\ReadModel\Material\MaterialById;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialGetByIdFetcher
{
    private const string TABLE = 'materials';
    private const int CACHE_TTL = 900;

    public function __construct(
        private Connection $connection,
        private Cacher $cacher,
    ) {}

    /**
     * @throws Exception
     */
    public function fetch(MaterialGetByIdQuery $query): MaterialById
    {
        $key = 'material_by_id_' . $query->id;

        /** @var MaterialById|null $cached */
        $cached = $this->cacher->get($key);

        if ($cached !== null) {
            return $cached;
        }

        $row = $this->connection->createQueryBuilder()
            ->select('id', 'name', 'description')
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

        /** @var array{id: int, name: string, description: string} $row */
        $result = MaterialById::fromRow($row);
        $this->cacher->set($key, $result, self::CACHE_TTL);

        return $result;
    }
}
