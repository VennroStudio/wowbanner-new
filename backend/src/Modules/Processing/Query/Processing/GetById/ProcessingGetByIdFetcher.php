<?php

declare(strict_types=1);

namespace App\Modules\Processing\Query\Processing\GetById;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Processing\ReadModel\Processing\ProcessingById;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProcessingGetByIdFetcher
{
    private const string TABLE = 'processings';

    public function __construct(
        private Connection $connection,
    ) {}

    /** @throws Exception */
    public function fetch(ProcessingGetByIdQuery $query): ProcessingById
    {
        $row = $this->connection->createQueryBuilder()
            ->select('id', 'name', 'description', 'type', 'cost_price', 'price')
            ->from(self::TABLE)
            ->where('id = :id')
            ->setParameter('id', $query->id)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            throw new DomainExceptionModule(
                module: 'processing',
                message: 'error.processing_not_found',
                code: 1
            );
        }

        return ProcessingById::fromRow($row);
    }
}
