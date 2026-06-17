<?php

declare(strict_types=1);

namespace App\Modules\Processing\Query\Processing\GetById;

use App\Components\Exception\DomainExceptionModule;
use App\Components\ReadModel\ReadModelFields;
use App\Modules\Processing\ReadModel\Processing\Interface\ProcessingModelInterface;
use App\Modules\Processing\ReadModel\Processing\ProcessingDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProcessingGetByIdFetcher
{
    private const string TABLE = 'processings';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of ProcessingModelInterface
     * @param class-string<T> $modelClass
     * @return T
     * @throws Exception
     */
    public function fetch(ProcessingGetByIdQuery $query, string $modelClass = ProcessingDetails::class): ProcessingModelInterface
    {
        $row = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select($modelClass::fields()))
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

        return $modelClass::fromRow($row);
    }
}
