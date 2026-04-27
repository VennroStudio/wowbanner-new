<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialProcessing\Delete;

use App\Modules\Material\Entity\MaterialProcessing\MaterialProcessingRepository;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;

final readonly class DeleteMaterialProcessingHandler
{
    public function __construct(
        private MaterialProcessingRepository $repository,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
    ) {}

    public function handle(DeleteMaterialProcessingCommand $command): void
    {
        $entity = $this->repository->getById($command->id);
        $materialId = $entity->materialId;
        $optionId = $entity->optionId;

        $this->repository->remove($entity);

        $this->materialQueryCacheInvalidator->invalidateMaterialAndOptionContext($materialId, $optionId);
    }
}
