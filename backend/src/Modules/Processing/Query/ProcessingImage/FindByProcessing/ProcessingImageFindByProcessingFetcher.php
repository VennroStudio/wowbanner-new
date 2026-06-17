<?php

declare(strict_types=1);

namespace App\Modules\Processing\Query\ProcessingImage\FindByProcessing;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Processing\ReadModel\ProcessingImage\Interface\ProcessingImageModelInterface;
use App\Modules\Processing\ReadModel\ProcessingImage\ProcessingImageByProcessing;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProcessingImageFindByProcessingFetcher
{
    private const string TABLE = 'processing_images';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of ProcessingImageModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(
        ProcessingImageFindByProcessingQuery $query,
        string $modelClass = ProcessingImageByProcessing::class,
    ): array {
        $rows = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select($modelClass::fields()))
            ->from(self::TABLE)
            ->where('processing_id = :processingId')
            ->setParameter('processingId', $query->processingId)
            ->executeQuery()
            ->fetchAllAssociative();

        return $modelClass::fromRows($rows);
    }
}
