<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingByArea\Delete;

use App\Components\Cacher\Cacher;
use App\Modules\Material\Entity\MaterialPricingByArea\MaterialPricingByAreaRepository;

final readonly class DeleteMaterialPricingByAreaHandler
{
    public function __construct(
        private MaterialPricingByAreaRepository $repository,
        private Cacher $cacher,
    ) {}

    public function handle(DeleteMaterialPricingByAreaCommand $command): void
    {
        $entity = $this->repository->getById($command->id);
        $materialId = $entity->materialId;
        $optionId = $entity->optionId;

        $this->repository->remove($entity);

        $this->cacher->deleteTag('material_pricing_by_area_by_material_id_' . $materialId . '_option_id_' . $optionId);
    }
}
