<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialOption\GetById;

use App\Components\Cacher\Cacher;
use App\Components\Exception\DomainExceptionModule;
use App\Modules\Material\ReadModel\MaterialOption\MaterialOptionById;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialOptionGetByIdFetcher
{
    private const string TABLE = 'material_options';
    private const int CACHE_TTL = 900;

    public function __construct(
        private Connection $connection,
        private Cacher $cacher,
    ) {}

    /**
     * @throws Exception
     */
    public function fetch(MaterialOptionGetByIdQuery $query): MaterialOptionById
    {
        $key = 'material_option_by_id_' . $query->id;

        /** @var MaterialOptionById|null $cached */
        $cached = $this->cacher->get($key);

        if ($cached !== null) {
            return $cached;
        }

        $row = $this->connection->createQueryBuilder()
            ->select('id', 'name', 'material_id', 'pricing_type', 'is_cut')
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

        /** @var array{
         *     id: int,
         *     name: string,
         *     material_id: int,
         *     pricing_type: int,
         *     is_cut: int|string|bool
         * } $row
         */
        $result = MaterialOptionById::fromRow($row);
        $this->cacher->set($key, $result, self::CACHE_TTL);

        return $result;
    }
}
