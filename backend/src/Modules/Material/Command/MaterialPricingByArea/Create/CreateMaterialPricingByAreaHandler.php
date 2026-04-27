<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingByArea\Create;

use App\Modules\Material\Entity\MaterialPricingByArea\Fields\Enums\AreaRangeType;
use App\Modules\Material\Entity\MaterialPricingByArea\Fields\Enums\DpiType;
use App\Modules\Material\Entity\MaterialPricingByArea\MaterialPricingByArea;
use App\Modules\Material\Entity\MaterialPricingByArea\MaterialPricingByAreaRepository;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;

final readonly class CreateMaterialPricingByAreaHandler
{
    public function __construct(
        private MaterialPricingByAreaRepository $repository,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
    ) {}

    public function handle(CreateMaterialPricingByAreaCommand $command): void
    {
        $this->materialQueryCacheInvalidator->invalidateMaterialAndOptionContext(
            $command->materialId,
            $command->optionId
        );

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
}
