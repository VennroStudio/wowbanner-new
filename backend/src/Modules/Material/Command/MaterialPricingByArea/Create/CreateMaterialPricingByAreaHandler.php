<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingByArea\Create;

use App\Components\Cacher\Cacher;
use App\Modules\Material\Entity\MaterialPricingByArea\Fields\Enums\AreaRangeType;
use App\Modules\Material\Entity\MaterialPricingByArea\Fields\Enums\DpiType;
use App\Modules\Material\Entity\MaterialPricingByArea\MaterialPricingByArea;
use App\Modules\Material\Entity\MaterialPricingByArea\MaterialPricingByAreaRepository;

final readonly class CreateMaterialPricingByAreaHandler
{
    public function __construct(
        private MaterialPricingByAreaRepository $repository,
        private Cacher $cacher,
    ) {}

    public function handle(CreateMaterialPricingByAreaCommand $command): void
    {
        $this->deleteCache($command->materialId, $command->optionId);

        $entity = MaterialPricingByArea::create(
            materialId: $command->materialId,
            optionId: $command->optionId,
            dpiType: DpiType::from($command->dpiType),
            areaRangeType: AreaRangeType::from($command->areaRangeType),
            price: $command->price,
            cost: $command->cost,
            printHours: $command->printHours,
        );

        $this->repository->add($entity);
    }

    private function deleteCache(int $materialId, int $optionId): void
    {
        $this->cacher->deleteTag('material_pricing_by_area_by_material_id_' . $materialId . '_option_id_' . $optionId);
    }
}
