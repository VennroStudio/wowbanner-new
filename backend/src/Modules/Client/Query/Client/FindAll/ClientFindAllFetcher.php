<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\Client\FindAll;

use App\Components\ReadModel\ModelCountItemsResult;
use App\Components\ReadModel\ReadModelFields;
use App\Modules\Client\Query\ClientCompany\FindByClientIds\ClientCompanyFindByClientIdsFetcher;
use App\Modules\Client\Query\ClientPhone\FindByClientIds\ClientPhoneFindByClientIdsFetcher;
use App\Modules\Client\ReadModel\Client\ClientListItem;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

final readonly class ClientFindAllFetcher
{
    public const string ALIAS = 'c';
    private const string TABLE = 'clients';

    public function __construct(
        private Connection $connection,
        private ClientCompanyFindByClientIdsFetcher $companyFetcher,
        private ClientPhoneFindByClientIdsFetcher $phoneFetcher,
    ) {}

    public function joinForFilter(QueryBuilder $qb, string $alias): void
    {
        $qb->leftJoin($alias, self::TABLE, self::ALIAS, self::ALIAS . '.id = ' . $alias . '.client_id');
    }

    /**
     * @return ModelCountItemsResult<ClientListItem>
     * @throws Exception
     */
    public function fetch(ClientFindAllQuery $query): ModelCountItemsResult
    {
        $qb = $this->connection->createQueryBuilder()
            ->from(self::TABLE, 'c');

        $this->companyFetcher->joinForFilter($qb, 'c');
        $this->phoneFetcher->joinForFilter($qb, 'c');

        if ($query->search !== null && $query->search !== '') {
            $phoneAlias = ClientPhoneFindByClientIdsFetcher::ALIAS;
            $companyAlias = ClientCompanyFindByClientIdsFetcher::ALIAS;
            $qb->andWhere(
                $qb->expr()->or(
                    'LOWER(c.old_full_name) LIKE LOWER(:search)',
                    'LOWER(c.last_name) LIKE LOWER(:search)',
                    'LOWER(c.first_name) LIKE LOWER(:search)',
                    'LOWER(c.middle_name) LIKE LOWER(:search)',
                    'LOWER(c.email) LIKE LOWER(:search)',
                    "LOWER({$phoneAlias}.phone) LIKE LOWER(:search)",
                    "LOWER({$companyAlias}.company_name) LIKE LOWER(:search)"
                )
            )->setParameter('search', '%' . $query->search . '%');
        }

        $countQb = clone $qb;
        $total = (int)$countQb->select('COUNT(DISTINCT c.id)')->executeQuery()->fetchOne();

        $qb->groupBy('c.id');

        $rows = $qb->select(...ReadModelFields::select(ClientListItem::fields(), 'c'))
            ->orderBy('c.id', 'DESC')
            ->setFirstResult($query->getOffset())
            ->setMaxResults($query->perPage)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<ClientListItem> $items */
        $items = ClientListItem::fromRows($rows);

        return new ModelCountItemsResult(
            items: $items,
            count: $total,
        );
    }
}
