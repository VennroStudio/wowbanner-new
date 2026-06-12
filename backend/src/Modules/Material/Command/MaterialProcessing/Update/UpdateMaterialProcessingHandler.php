<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialProcessing\Update;

use App\Components\Cacher\Cacher;
use App\Modules\Material\Entity\MaterialProcessing\MaterialProcessingRepository;
use App\Modules\Processing\Query\Processing\GetById\ProcessingGetByIdFetcher;
use App\Modules\Processing\Query\Processing\GetById\ProcessingGetByIdQuery;
use Doctrine\DBAL\Exception as DbalException;

final readonly class UpdateMaterialProcessingHandler
{
    public function __construct(
        private MaterialProcessingRepository $repository,
        private Cacher $cacher,
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
            $this->deleteCache($prevMaterialId, $prevOptionId);
        }
        $this->deleteCache($command->materialId, $command->optionId);
    }

    private function deleteCache(int $materialId, int $optionId): void
    {
        $this->cacher->deleteTag('material_processing_by_material_id_' . $materialId . '_option_id_' . $optionId);
    }
}
