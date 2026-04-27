<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialOption\Create;

use App\Modules\Material\Entity\MaterialOption\Fields\Enums\MaterialOptionPricingType;
use App\Modules\Material\Entity\MaterialOption\MaterialOption;
use App\Modules\Material\Entity\MaterialOption\MaterialOptionRepository;

final readonly class CreateMaterialOptionHandler
{
    public function __construct(
        private MaterialOptionRepository $optionRepository,
    ) {}

    public function handle(CreateMaterialOptionCommand $command): MaterialOption
    {
        $option = MaterialOption::create(
            name: $command->name,
            materialId: $command->materialId,
            pricingType: MaterialOptionPricingType::from($command->pricingType),
            isCut: $command->isCut,
        );

        $this->optionRepository->add($option);

        return $option;
    }
}
