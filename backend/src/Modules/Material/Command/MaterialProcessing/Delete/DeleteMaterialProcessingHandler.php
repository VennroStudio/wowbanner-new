<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialProcessing\Delete;

use App\Components\Cacher\Cacher;
use App\Modules\Material\Entity\MaterialProcessing\MaterialProcessingRepository;

final readonly class DeleteMaterialProcessingHandler
{
    public function __construct(
        private MaterialProcessingRepository $repository,
        private Cacher $cacher,
    ) {}

    public function handle(DeleteMaterialProcessingCommand $command): void
    {
        $entity = $this->repository->getById($command->id);
        $materialId = $entity->materialId;
        $optionId = $entity->optionId;

        $this->repository->remove($entity);

        $this->cacher->deleteTag('material_processing_by_material_id_' . $materialId . '_option_id_' . $optionId);
    }
}
