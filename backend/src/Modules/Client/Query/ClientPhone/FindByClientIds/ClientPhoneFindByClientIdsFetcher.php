<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\ClientPhone\FindByClientIds;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Client\ReadModel\ClientPhone\ClientPhoneSummary;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

final readonly class ClientPhoneFindByClientIdsFetcher
{
    public const string ALIAS = 'cp';
    private const string TABLE = 'client_phones';

    public function __construct(
        private Connection $connection,
    ) {}

    public function joinForFilter(QueryBuilder $qb, string $alias): void
    {
        $qb->leftJoin($alias, self::TABLE, self::ALIAS, self::ALIAS . '.client_id = ' . $alias . '.id');
    }

    /**
     * @return list<ClientPhoneSummary>
     * @throws Exception
     */
    public function fetch(ClientPhoneFindByClientIdsQuery $query): array
    {
        if (empty($query->clientIds)) {
            return [];
        }

        $rows = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select(ClientPhoneSummary::fields()))
            ->from(self::TABLE)
            ->where('client_id IN (:clientIds)')
            ->setParameter('clientIds', $query->clientIds, ArrayParameterType::INTEGER)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<ClientPhoneSummary> $items */
        return ClientPhoneSummary::fromRows($rows);
    }
}
