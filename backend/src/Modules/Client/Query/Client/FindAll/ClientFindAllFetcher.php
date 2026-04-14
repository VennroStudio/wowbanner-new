<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\Client\FindAll;

use App\Components\ReadModel\ModelCountItemsResult;
use App\Modules\Client\Query\ClientCompany\FindByClientIds\ClientCompanyFindByClientIdsFetcher;
use App\Modules\Client\Query\ClientPhone\FindByClientIds\ClientPhoneFindByClientIdsFetcher;
use App\Modules\Client\ReadModel\Client\ClientFindAll;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ClientFindAllFetcher
{
    private const string TABLE = 'clients';

    public function __construct(
        private Connection $connection,
        private ClientCompanyFindByClientIdsFetcher $companyFetcher,
        private ClientPhoneFindByClientIdsFetcher $phoneFetcher,
    ) {}

    /**
     * @return ModelCountItemsResult<ClientFindAll>
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

        $rows = $qb->select(
            'c.id',
            'c.old_full_name',
            'c.last_name',
            'c.first_name',
            'c.middle_name',
            'c.email',
            'c.docs',
            'c.type',
            'c.created_at'
        )
            ->orderBy('c.id', 'DESC')
            ->setFirstResult($query->getOffset())
            ->setMaxResults($query->perPage)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<ClientFindAll> $items */
        $items = ClientFindAll::fromRows($rows);

        return new ModelCountItemsResult(
            items: $items,
            count: $total,
        );
    }
}
