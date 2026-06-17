<?php

declare(strict_types=1);

namespace App\Modules\Processing\Query\Processing\FindAll;

use App\Components\ReadModel\ModelCountItemsResult;
use App\Components\ReadModel\ReadModelFields;
use App\Modules\Processing\ReadModel\Processing\Interface\ProcessingModelInterface;
use App\Modules\Processing\ReadModel\Processing\ProcessingDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProcessingFindAllFetcher
{
    private const string TABLE = 'processings';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of ProcessingModelInterface
     * @param class-string<T> $modelClass
     * @return ModelCountItemsResult<T>
     * @throws Exception
     */
    public function fetch(ProcessingFindAllQuery $query, string $modelClass = ProcessingDetails::class): ModelCountItemsResult
    {
        $qb = $this->connection->createQueryBuilder()
            ->from(self::TABLE);

        if ($query->search !== null && $query->search !== '') {
            $qb->andWhere('LOWER(name) LIKE LOWER(:search)')
                ->setParameter('search', '%' . $query->search . '%');
        }

        $total = (int)(clone $qb)->select('COUNT(id)')->executeQuery()->fetchOne();

        $rows = $qb
            ->select(...ReadModelFields::select($modelClass::fields()))
            ->orderBy('id', 'ASC')
            ->setFirstResult($query->getOffset())
            ->setMaxResults($query->perPage)
            ->executeQuery()
            ->fetchAllAssociative();

        return new ModelCountItemsResult(
            items: $modelClass::fromRows($rows),
            count: $total,
        );
    }
}
