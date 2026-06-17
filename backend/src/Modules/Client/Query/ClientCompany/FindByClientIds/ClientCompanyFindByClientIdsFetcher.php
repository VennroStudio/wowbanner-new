<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\ClientCompany\FindByClientIds;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Client\ReadModel\ClientCompany\ClientCompanySummary;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

final readonly class ClientCompanyFindByClientIdsFetcher
{
    public const string ALIAS = 'cc';
    private const string TABLE = 'client_companies';

    public function __construct(
        private Connection $connection,
    ) {}

    public function joinForFilter(QueryBuilder $qb, string $alias): void
    {
        $qb->leftJoin($alias, self::TABLE, self::ALIAS, self::ALIAS . '.client_id = ' . $alias . '.id');
    }

    /**
     * @return list<ClientCompanySummary>
     * @throws Exception
     */
    public function fetch(ClientCompanyFindByClientIdsQuery $query): array
    {
        if (empty($query->clientIds)) {
            return [];
        }

        $rows = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select(ClientCompanySummary::fields()))
            ->from(self::TABLE)
            ->where('client_id IN (:clientIds)')
            ->setParameter('clientIds', $query->clientIds, ArrayParameterType::INTEGER)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<ClientCompanySummary> $items */
        return ClientCompanySummary::fromRows($rows);
    }
}
