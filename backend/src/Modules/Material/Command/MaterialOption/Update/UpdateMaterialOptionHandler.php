<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialOption\Update;

use App\Modules\Material\Entity\MaterialOption\Fields\Enums\MaterialOptionPricingType;
use App\Modules\Material\Entity\MaterialOption\MaterialOptionRepository;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;

final readonly class UpdateMaterialOptionHandler
{
    public function __construct(
        private MaterialOptionRepository $optionRepository,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
    ) {}

    public function handle(UpdateMaterialOptionCommand $command): void
    {
        $option = $this->optionRepository->getById($command->id);
        $materialId = $option->materialId;

        $option->edit(
            name: $command->name,
            pricingType: MaterialOptionPricingType::from($command->pricingType),
            isCut: $command->isCut,
        );

        $this->materialQueryCacheInvalidator->invalidateMaterialOption($command->id, $materialId);
    }
}
