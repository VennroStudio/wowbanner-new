<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialPricingCut;

use App\Modules\Material\Entity\MaterialPricingCut\Fields\Enums\MaterialPricingCutType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'material_pricing_cuts')]
#[ORM\Index(name: 'idx_m_pricing_cut_material_id', columns: ['material_id'])]
#[ORM\Index(name: 'idx_m_pricing_cut_option_id', columns: ['option_id'])]
class MaterialPricingCut
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $materialId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $optionId;

    #[ORM\Column(name: 'type', type: Types::INTEGER, enumType: MaterialPricingCutType::class)]
    private(set) MaterialPricingCutType $type;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $price;

    private function __construct(
        int $materialId,
        int $optionId,
        MaterialPricingCutType $type,
        string $price,
    ) {
        $this->materialId = $materialId;
        $this->optionId = $optionId;
        $this->type = $type;
        $this->price = $price;
    }

    public static function create(
        int $materialId,
        int $optionId,
        MaterialPricingCutType $type,
        string $price,
    ): self {
        return new self($materialId, $optionId, $type, $price);
    }

    public function edit(MaterialPricingCutType $type, string $price): void
    {
        $this->type = $type;
        $this->price = $price;
    }
}
