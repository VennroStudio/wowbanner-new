<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingCut\Create;

use App\Modules\Material\Entity\MaterialPricingCut\Fields\Enums\MaterialPricingCutType;
use App\Modules\Material\Entity\MaterialPricingCut\MaterialPricingCut;
use App\Modules\Material\Entity\MaterialPricingCut\MaterialPricingCutRepository;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;

final readonly class CreateMaterialPricingCutHandler
{
    public function __construct(
        private MaterialPricingCutRepository $repository,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
    ) {}

    public function handle(CreateMaterialPricingCutCommand $command): void
    {
        $this->materialQueryCacheInvalidator->invalidateMaterialAndOptionContext(
            $command->materialId,
            $command->optionId
        );

        $entity = MaterialPricingCut::create(
            materialId: $command->materialId,
            optionId: $command->optionId,
            type: MaterialPricingCutType::from($command->type),
            price: $command->price,
        );

        $this->repository->add($entity);
    }
}
