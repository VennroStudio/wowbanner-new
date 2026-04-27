<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingCut\Update;

use App\Modules\Material\Entity\MaterialPricingCut\Fields\Enums\MaterialPricingCutType;
use App\Modules\Material\Entity\MaterialPricingCut\MaterialPricingCutRepository;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;

final readonly class UpdateMaterialPricingCutHandler
{
    public function __construct(
        private MaterialPricingCutRepository $repository,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
    ) {}

    public function handle(UpdateMaterialPricingCutCommand $command): void
    {
        $entity = $this->repository->getById($command->id);

        $entity->edit(
            type: MaterialPricingCutType::from($command->type),
            price: $command->price,
        );

        $this->materialQueryCacheInvalidator->invalidateMaterialAndOptionContext(
            $entity->materialId,
            $entity->optionId
        );
    }
}
