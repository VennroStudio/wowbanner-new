<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingByPiece\Delete;

use App\Components\Cacher\Cacher;
use App\Modules\Material\Entity\MaterialPricingByPiece\MaterialPricingByPieceRepository;

final readonly class DeleteMaterialPricingByPieceHandler
{
    public function __construct(
        private MaterialPricingByPieceRepository $repository,
        private Cacher $cacher,
    ) {}

    public function handle(DeleteMaterialPricingByPieceCommand $command): void
    {
        $entity = $this->repository->getById($command->id);
        $materialId = $entity->materialId;
        $optionId = $entity->optionId;

        $this->repository->remove($entity);

        $this->cacher->deleteTag(
            'material_pricing_by_piece_by_material_id_' . $materialId . '_option_id_' . $optionId
        );
    }
}
