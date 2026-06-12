<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingCut\Update;

use App\Components\Cacher\Cacher;
use App\Modules\Material\Entity\MaterialPricingCut\Fields\Enums\MaterialPricingCutType;
use App\Modules\Material\Entity\MaterialPricingCut\MaterialPricingCutRepository;

final readonly class UpdateMaterialPricingCutHandler
{
    public function __construct(
        private MaterialPricingCutRepository $repository,
        private Cacher $cacher,
    ) {}

    public function handle(UpdateMaterialPricingCutCommand $command): void
    {
        $entity = $this->repository->getById($command->id);

        $entity->edit(
            type: MaterialPricingCutType::from($command->type),
            price: $command->price,
        );

        $this->cacher->deleteTag(
            'material_pricing_cut_by_material_id_' . $entity->materialId . '_option_id_' . $entity->optionId
        );
    }
}
