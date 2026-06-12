<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialOption\Update;

use App\Components\Cacher\Cacher;
use App\Modules\Material\Entity\MaterialOption\Fields\Enums\MaterialOptionPricingType;
use App\Modules\Material\Entity\MaterialOption\MaterialOptionRepository;

final readonly class UpdateMaterialOptionHandler
{
    public function __construct(
        private MaterialOptionRepository $optionRepository,
        private Cacher $cacher,
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

        $this->cacher->deleteTag('material_option_by_id_' . $command->id);
        $this->cacher->deleteTag('material_option_by_material_id_' . $materialId);
    }
}
