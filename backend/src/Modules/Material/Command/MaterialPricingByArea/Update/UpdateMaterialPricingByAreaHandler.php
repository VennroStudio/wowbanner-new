<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingByArea\Update;

use App\Components\Cacher\Cacher;
use App\Modules\Material\Entity\MaterialPricingByArea\Fields\Enums\AreaRangeType;
use App\Modules\Material\Entity\MaterialPricingByArea\Fields\Enums\DpiType;
use App\Modules\Material\Entity\MaterialPricingByArea\MaterialPricingByAreaRepository;

final readonly class UpdateMaterialPricingByAreaHandler
{
    public function __construct(
        private MaterialPricingByAreaRepository $repository,
        private Cacher $cacher,
    ) {}

    public function handle(UpdateMaterialPricingByAreaCommand $command): void
    {
        $entity = $this->repository->getById($command->id);

        $entity->edit(
            dpiType: DpiType::from($command->dpiType),
            areaRangeType: AreaRangeType::from($command->areaRangeType),
            price: $command->price,
            cost: $command->cost,
            printHours: $command->printHours,
        );

        $this->cacher->deleteTag(
            'material_pricing_by_area_by_material_id_' . $entity->materialId . '_option_id_' . $entity->optionId
        );
    }
}
