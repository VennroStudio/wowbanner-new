<?php

declare(strict_types=1);

namespace App\Modules\Production\Entity\ProductionMaterial;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'production_materials')]
#[ORM\Index(columns: ['production_id'], name: 'idx_production_id')]
#[ORM\Index(columns: ['material_option_id'], name: 'idx_material_option_id')]
class ProductionMaterial
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $productionId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $materialOptionId;

    private function __construct(int $productionId, int $materialOptionId)
    {
        $this->productionId = $productionId;
        $this->materialOptionId = $materialOptionId;
    }

    public static function create(int $productionId, int $materialOptionId): self
    {
        return new self($productionId, $materialOptionId);
    }

    public function edit(int $productionId, int $materialOptionId): void
    {
        $this->productionId = $productionId;
        $this->materialOptionId = $materialOptionId;
    }
}
