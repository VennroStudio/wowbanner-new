<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialOption\Create;

use App\Components\Cacher\Cacher;
use App\Modules\Material\Entity\MaterialOption\Fields\Enums\MaterialOptionPricingType;
use App\Modules\Material\Entity\MaterialOption\MaterialOption;
use App\Modules\Material\Entity\MaterialOption\MaterialOptionRepository;

final readonly class CreateMaterialOptionHandler
{
    public function __construct(
        private MaterialOptionRepository $optionRepository,
        private Cacher $cacher,
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
        $this->cacher->deleteTag('material_option_by_material_id_' . $command->materialId);

        return $option;
    }
}
