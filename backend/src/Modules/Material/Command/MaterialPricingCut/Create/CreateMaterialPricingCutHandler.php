<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingCut\Create;

use App\Components\Cacher\Cacher;
use App\Modules\Material\Entity\MaterialPricingCut\Fields\Enums\MaterialPricingCutType;
use App\Modules\Material\Entity\MaterialPricingCut\MaterialPricingCut;
use App\Modules\Material\Entity\MaterialPricingCut\MaterialPricingCutRepository;

final readonly class CreateMaterialPricingCutHandler
{
    public function __construct(
        private MaterialPricingCutRepository $repository,
        private Cacher $cacher,
    ) {}

    public function handle(CreateMaterialPricingCutCommand $command): void
    {
        $this->deleteCache($command->materialId, $command->optionId);

        $entity = MaterialPricingCut::create(
            materialId: $command->materialId,
            optionId: $command->optionId,
            type: MaterialPricingCutType::from($command->type),
            price: $command->price,
        );

        $this->repository->add($entity);
    }

    private function deleteCache(int $materialId, int $optionId): void
    {
        $this->cacher->deleteTag('material_pricing_cut_by_material_id_' . $materialId . '_option_id_' . $optionId);
    }
}
