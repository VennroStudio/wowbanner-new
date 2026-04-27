<?php

declare(strict_types=1);

namespace App\Modules\Material\Service;

use App\Components\Cacher\Cacher;

final readonly class MaterialQueryCacheInvalidator
{
    public function __construct(
        private Cacher $cacher,
    ) {}

    public function invalidateMaterialOption(int $id, int $materialId): void
    {
        $this->cacher->delete('material_option_by_id_' . $id);
        $this->cacher->delete('material_option_by_material_id_' . $materialId);
    }

    public function invalidateByMaterialId(int $materialId): void
    {
        $this->cacher->delete('material_option_by_material_id_' . $materialId);
    }

    public function invalidateMaterialAndOptionContext(int $materialId, int $optionId): void
    {
        $suffix = $materialId . '_option_id_' . $optionId;
        $this->cacher->delete('material_processing_by_material_id_' . $suffix);
        $this->cacher->delete('material_pricing_by_area_by_material_id_' . $suffix);
        $this->cacher->delete('material_pricing_by_piece_by_material_id_' . $suffix);
        $this->cacher->delete('material_pricing_cut_by_material_id_' . $suffix);
    }
}
