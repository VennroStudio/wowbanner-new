<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialOption\Delete;

use App\Components\Cacher\Cacher;
use App\Modules\Material\Entity\MaterialOption\MaterialOptionRepository;

final readonly class DeleteMaterialOptionHandler
{
    public function __construct(
        private MaterialOptionRepository $optionRepository,
        private Cacher $cacher,
    ) {}

    public function handle(DeleteMaterialOptionCommand $command): void
    {
        $option = $this->optionRepository->getById($command->id);
        $materialId = $option->materialId;
        $optionId = $command->id;

        $this->optionRepository->remove($option);

        $this->cacher->deleteTag('material_option_by_id_' . $optionId);
        $this->cacher->deleteTag('material_option_by_material_id_' . $materialId);
        $this->cacher->deleteTag('material_processing_by_material_id_' . $materialId . '_option_id_' . $optionId);
        $this->cacher->deleteTag('material_pricing_by_area_by_material_id_' . $materialId . '_option_id_' . $optionId);
        $this->cacher->deleteTag('material_pricing_by_piece_by_material_id_' . $materialId . '_option_id_' . $optionId);
        $this->cacher->deleteTag('material_pricing_cut_by_material_id_' . $materialId . '_option_id_' . $optionId);
    }
}
