<?php

declare(strict_types=1);

namespace App\Modules\Processing\Query\ProcessingImage\FindByProcessingIds;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Processing\ReadModel\ProcessingImage\Interface\ProcessingImageModelInterface;
use App\Modules\Processing\ReadModel\ProcessingImage\ProcessingImageByProcessing;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProcessingImageFindByProcessingIdsFetcher
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
        ProcessingImageFindByProcessingIdsQuery $query,
        string $modelClass = ProcessingImageByProcessing::class,
    ): array {
        if ($query->processingIds === []) {
            return [];
        }

        $rows = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select($modelClass::fields()))
            ->from(self::TABLE)
            ->where('processing_id IN (:ids)')
            ->setParameter('ids', $query->processingIds, ArrayParameterType::INTEGER)
            ->orderBy('id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        return $modelClass::fromRows($rows);
    }
}
