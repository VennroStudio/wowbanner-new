<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingByPiece\Update;

use App\Components\Cacher\Cacher;
use App\Modules\Material\Entity\MaterialPricingByPiece\Fields\Enums\VariantType;
use App\Modules\Material\Entity\MaterialPricingByPiece\MaterialPricingByPieceRepository;

final readonly class UpdateMaterialPricingByPieceHandler
{
    public function __construct(
        private MaterialPricingByPieceRepository $repository,
        private Cacher $cacher,
    ) {}

    public function handle(UpdateMaterialPricingByPieceCommand $command): void
    {
        $entity = $this->repository->getById($command->id);

        $entity->edit(
            variantType: VariantType::from($command->variantType),
            price: $command->price,
            cost: $command->cost,
            printHours: $command->printHours,
        );

        $this->cacher->deleteTag(
            'material_pricing_by_piece_by_material_id_' . $entity->materialId . '_option_id_' . $entity->optionId
        );
    }
}
