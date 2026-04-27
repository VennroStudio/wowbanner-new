<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialProcessing\Create;

use App\Modules\Material\Entity\MaterialProcessing\MaterialProcessing;
use App\Modules\Material\Entity\MaterialProcessing\MaterialProcessingRepository;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;
use App\Modules\Processing\Query\Processing\GetById\ProcessingGetByIdFetcher;
use App\Modules\Processing\Query\Processing\GetById\ProcessingGetByIdQuery;
use Doctrine\DBAL\Exception as DbalException;

final readonly class CreateMaterialProcessingHandler
{
    public function __construct(
        private MaterialProcessingRepository $repository,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
        private ProcessingGetByIdFetcher $processingGetByIdFetcher,
    ) {}

    /**
     * @throws DbalException
     */
    public function handle(CreateMaterialProcessingCommand $command): void
    {
        $this->processingGetByIdFetcher->fetch(
            new ProcessingGetByIdQuery($command->processingId)
        );

        $this->materialQueryCacheInvalidator->invalidateMaterialAndOptionContext(
            $command->materialId,
            $command->optionId
        );

        $entity = MaterialProcessing::create(
            materialId: $command->materialId,
            optionId: $command->optionId,
            processingId: $command->processingId,
        );

        $this->repository->add($entity);
    }
}
