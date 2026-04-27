<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingByArea\Delete;

use App\Modules\Material\Entity\MaterialPricingByArea\MaterialPricingByAreaRepository;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;

final readonly class DeleteMaterialPricingByAreaHandler
{
    public function __construct(
        private MaterialPricingByAreaRepository $repository,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
    ) {}

    public function handle(DeleteMaterialPricingByAreaCommand $command): void
    {
        $entity = $this->repository->getById($command->id);
        $materialId = $entity->materialId;
        $optionId = $entity->optionId;

        $this->repository->remove($entity);

        $this->materialQueryCacheInvalidator->invalidateMaterialAndOptionContext($materialId, $optionId);
    }
}
