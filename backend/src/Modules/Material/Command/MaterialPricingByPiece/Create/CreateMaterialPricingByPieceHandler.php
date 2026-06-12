<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingByPiece\Create;

use App\Components\Cacher\Cacher;
use App\Modules\Material\Entity\MaterialPricingByPiece\Fields\Enums\VariantType;
use App\Modules\Material\Entity\MaterialPricingByPiece\MaterialPricingByPiece;
use App\Modules\Material\Entity\MaterialPricingByPiece\MaterialPricingByPieceRepository;

final readonly class CreateMaterialPricingByPieceHandler
{
    public function __construct(
        private MaterialPricingByPieceRepository $repository,
        private Cacher $cacher,
    ) {}

    public function handle(CreateMaterialPricingByPieceCommand $command): void
    {
        $this->deleteCache($command->materialId, $command->optionId);

        $entity = MaterialPricingByPiece::create(
            materialId: $command->materialId,
            optionId: $command->optionId,
            variantType: VariantType::from($command->variantType),
            price: $command->price,
            cost: $command->cost,
            printHours: $command->printHours,
        );

        $this->repository->add($entity);
    }

    private function deleteCache(int $materialId, int $optionId): void
    {
        $this->cacher->deleteTag(
            'material_pricing_by_piece_by_material_id_' . $materialId . '_option_id_' . $optionId
        );
    }
}
