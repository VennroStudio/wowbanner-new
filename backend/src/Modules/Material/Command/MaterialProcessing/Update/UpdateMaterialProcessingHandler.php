<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialProcessing\Update;

use App\Modules\Material\Entity\MaterialProcessing\MaterialProcessingRepository;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;
use App\Modules\Processing\Query\Processing\GetById\ProcessingGetByIdFetcher;
use App\Modules\Processing\Query\Processing\GetById\ProcessingGetByIdQuery;
use Doctrine\DBAL\Exception as DbalException;

final readonly class UpdateMaterialProcessingHandler
{
    public function __construct(
        private MaterialProcessingRepository $repository,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
        private ProcessingGetByIdFetcher $processingGetByIdFetcher,
    ) {}

    /**
     * @throws DbalException
     */
    public function handle(UpdateMaterialProcessingCommand $command): void
    {
        $entity = $this->repository->getById($command->id);
        $prevMaterialId = $entity->materialId;
        $prevOptionId = $entity->optionId;

        $this->processingGetByIdFetcher->fetch(
            new ProcessingGetByIdQuery($command->processingId)
        );

        $entity->edit(
            materialId: $command->materialId,
            optionId: $command->optionId,
            processingId: $command->processingId,
        );

        if ($prevMaterialId !== $command->materialId || $prevOptionId !== $command->optionId) {
            $this->materialQueryCacheInvalidator->invalidateMaterialAndOptionContext(
                $prevMaterialId,
                $prevOptionId
            );
        }
        $this->materialQueryCacheInvalidator->invalidateMaterialAndOptionContext(
            $command->materialId,
            $command->optionId
        );
    }
}
