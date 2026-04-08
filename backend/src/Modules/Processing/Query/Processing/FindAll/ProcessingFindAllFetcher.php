<?php

declare(strict_types=1);

namespace App\Modules\Processing\Query\Processing\FindAll;

use App\Components\Query\ModelCountItemsResult;
use App\Modules\Processing\ReadModel\Processing\ProcessingFindAll;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProcessingFindAllFetcher
{
    private const string TABLE = 'processings';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return ModelCountItemsResult<ProcessingFindAll>
     * @throws Exception
     */
    public function fetch(ProcessingFindAllQuery $query): ModelCountItemsResult
    {
        $qb = $this->connection->createQueryBuilder()
            ->from(self::TABLE);

        if ($query->search !== null && $query->search !== '') {
            $qb->andWhere('name ILIKE :search')
                ->setParameter('search', '%' . $query->search . '%');
        }

        $total = (int) (clone $qb)->select('COUNT(id)')->executeQuery()->fetchOne();

        $rows = $qb
            ->select('id', 'name', 'type', 'price')
            ->orderBy('id', 'ASC')
            ->setFirstResult($query->getOffset())
            ->setMaxResults($query->perPage)
            ->executeQuery()
            ->fetchAllAssociative();

        return new ModelCountItemsResult(
            items: ProcessingFindAll::fromRows($rows),
            count: $total,
        );
    }
}
