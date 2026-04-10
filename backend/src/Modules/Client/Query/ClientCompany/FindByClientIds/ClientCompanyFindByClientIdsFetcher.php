<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\ClientCompany\FindByClientIds;

use App\Modules\Client\ReadModel\ClientCompany\ClientCompanyByClient;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

final readonly class ClientCompanyFindByClientIdsFetcher
{
    private const string TABLE = 'client_companies';
    public const string ALIAS = 'cc';

    public function __construct(
        private Connection $connection,
    ) {}

    public function joinForFilter(QueryBuilder $qb, string $alias): void
    {
        $qb->leftJoin($alias, self::TABLE, self::ALIAS, self::ALIAS . '.client_id = ' . $alias . '.id');
    }

    /**
     * @return list<ClientCompanyByClient>
     * @throws Exception
     */
    public function fetch(ClientCompanyFindByClientIdsQuery $query): array
    {
        if (empty($query->clientIds)) {
            return [];
        }

        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'client_id', 'company_name')
            ->from(self::TABLE)
            ->where('client_id IN (:clientIds)')
            ->setParameter('clientIds', $query->clientIds, ArrayParameterType::INTEGER)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<ClientCompanyByClient> $items */
        $items = ClientCompanyByClient::fromRows($rows);

        return $items;
    }
}
