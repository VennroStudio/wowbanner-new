<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingCut\Delete;

use App\Components\Cacher\Cacher;
use App\Modules\Material\Entity\MaterialPricingCut\MaterialPricingCutRepository;

final readonly class DeleteMaterialPricingCutHandler
{
    public function __construct(
        private MaterialPricingCutRepository $repository,
        private Cacher $cacher,
    ) {}

    public function handle(DeleteMaterialPricingCutCommand $command): void
    {
        $entity = $this->repository->getById($command->id);
        $materialId = $entity->materialId;
        $optionId = $entity->optionId;

        $this->repository->remove($entity);

        $this->cacher->deleteTag('material_pricing_cut_by_material_id_' . $materialId . '_option_id_' . $optionId);
    }
}
