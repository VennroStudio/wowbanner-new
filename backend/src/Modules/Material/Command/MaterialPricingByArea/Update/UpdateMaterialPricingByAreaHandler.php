<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingByArea\Update;

use App\Modules\Material\Entity\MaterialPricingByArea\Fields\Enums\AreaRangeType;
use App\Modules\Material\Entity\MaterialPricingByArea\Fields\Enums\DpiType;
use App\Modules\Material\Entity\MaterialPricingByArea\MaterialPricingByAreaRepository;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;

final readonly class UpdateMaterialPricingByAreaHandler
{
    public function __construct(
        private MaterialPricingByAreaRepository $repository,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
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

        $this->materialQueryCacheInvalidator->invalidateMaterialAndOptionContext(
            $entity->materialId,
            $entity->optionId
        );
    }
}
