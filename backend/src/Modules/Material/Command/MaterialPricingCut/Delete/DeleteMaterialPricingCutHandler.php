<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingCut\Delete;

use App\Modules\Material\Entity\MaterialPricingCut\MaterialPricingCutRepository;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;

final readonly class DeleteMaterialPricingCutHandler
{
    public function __construct(
        private MaterialPricingCutRepository $repository,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
    ) {}

    public function handle(DeleteMaterialPricingCutCommand $command): void
    {
        $entity = $this->repository->getById($command->id);
        $materialId = $entity->materialId;
        $optionId = $entity->optionId;

        $this->repository->remove($entity);

        $this->materialQueryCacheInvalidator->invalidateMaterialAndOptionContext($materialId, $optionId);
    }
}
