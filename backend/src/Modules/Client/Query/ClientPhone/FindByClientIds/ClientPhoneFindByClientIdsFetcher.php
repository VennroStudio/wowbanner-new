<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\ClientPhone\FindByClientIds;

use App\Modules\Client\ReadModel\ClientPhone\ClientPhoneByClient;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

final readonly class ClientPhoneFindByClientIdsFetcher
{
    private const string TABLE = 'client_phones';
    public const string ALIAS = 'cp';

    public function __construct(
        private Connection $connection,
    ) {}

    public function joinForFilter(QueryBuilder $qb, string $alias): void
    {
        $qb->leftJoin($alias, self::TABLE, self::ALIAS, self::ALIAS . '.client_id = ' . $alias . '.id');
    }

    /**
     * @return list<ClientPhoneByClient>
     * @throws Exception
     */
    public function fetch(ClientPhoneFindByClientIdsQuery $query): array
    {
        if (empty($query->clientIds)) {
            return [];
        }

        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'client_id', 'type', 'phone')
            ->from(self::TABLE)
            ->where('client_id IN (:clientIds)')
            ->setParameter('clientIds', $query->clientIds, ArrayParameterType::INTEGER)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<ClientPhoneByClient> $items */
        $items = ClientPhoneByClient::fromRows($rows);

        return $items;
    }
}
