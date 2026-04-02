<?php

declare(strict_types=1);

namespace App\Modules\Printing\Query\Printing\FindAll;

use App\Components\ReadModel\ModelCountItemsResult;
use App\Modules\Printing\ReadModel\Printing\PrintingFindAll;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class PrintingFindAllFetcher
{
    private const string TABLE = 'printings';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return ModelCountItemsResult<PrintingFindAll>
     * @throws Exception
     */
    public function fetch(PrintingFindAllQuery $query): ModelCountItemsResult
    {
        $qb = $this->connection->createQueryBuilder()
            ->from(self::TABLE);

        if ($query->search !== null && $query->search !== '') {
            $qb->andWhere('name LIKE :search')
                ->setParameter('search', '%' . $query->search . '%');
        }

        $countQb = clone $qb;
        $total = (int)$countQb->select('COUNT(id)')->executeQuery()->fetchOne();

        $rows = $qb->select('id', 'name')
            ->orderBy('id', 'DESC')
            ->setFirstResult($query->getOffset())
            ->setMaxResults($query->perPage)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<PrintingFindAll> $items */
        $items = PrintingFindAll::fromRows($rows);

        return new ModelCountItemsResult(
            items: $items,
            count: $total,
        );
    }
}
