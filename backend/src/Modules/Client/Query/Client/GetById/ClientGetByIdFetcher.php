<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\Client\GetById;

use App\Components\Cacher\Cacher;
use App\Components\Exception\DomainExceptionModule;
use App\Modules\Client\ReadModel\Client\ClientById;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ClientGetByIdFetcher
{
    private const string TABLE = 'clients';
    private const int CACHE_TTL = 900;

    public function __construct(
        private Connection $connection,
        private Cacher $cacher,
    ) {}

    /**
     * @throws Exception
     */
    public function fetch(ClientGetByIdQuery $query): ClientById
    {
        $key = 'client_by_id_' . $query->id;

        /** @var ClientById|null $cached */
        $cached = $this->cacher->get($key);

        if ($cached !== null) {
            return $cached;
        }

        $row = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'old_full_name',
                'last_name',
                'first_name',
                'middle_name',
                'email',
                'info',
                'docs',
                'type',
                'created_at',
                'updated_at'
            )
            ->from(self::TABLE)
            ->where('id = :id')
            ->setParameter('id', $query->id)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            throw new DomainExceptionModule(
                module: 'client',
                message: 'error.client_not_found',
                code: 1
            );
        }

        /**
         * @var array{
         *     id: int,
         *     old_full_name: string|null,
         *     last_name: string,
         *     first_name: string,
         *     middle_name: string|null,
         *     email: string|null,
         *     info: string|null,
         *     docs: int,
         *     type: int,
         *     created_at: string,
         *     updated_at: string|null
         * } $row
         */
        $result = ClientById::fromRow($row);
        $this->cacher->set($key, $result, self::CACHE_TTL);

        return $result;
    }
}
