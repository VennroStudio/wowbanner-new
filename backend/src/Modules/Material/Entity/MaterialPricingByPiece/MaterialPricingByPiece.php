<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialPricingByPiece;

use App\Modules\Material\Entity\MaterialPricingByPiece\Fields\Enums\VariantType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'material_pricing_by_piece')]
#[ORM\Index(name: 'idx_m_pricing_piece_material_id', columns: ['material_id'])]
#[ORM\Index(name: 'idx_m_pricing_piece_option_id', columns: ['option_id'])]
class MaterialPricingByPiece
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $materialId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $optionId;

    #[ORM\Column(type: Types::INTEGER, enumType: VariantType::class)]
    private(set) VariantType $variantType;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $price;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $cost;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $printHours;

    private function __construct(
        int $materialId,
        int $optionId,
        VariantType $variantType,
        string $price,
        string $cost,
        string $printHours,
    ) {
        $this->materialId = $materialId;
        $this->optionId = $optionId;
        $this->variantType = $variantType;
        $this->price = $price;
        $this->cost = $cost;
        $this->printHours = $printHours;
    }

    public static function create(
        int $materialId,
        int $optionId,
        VariantType $variantType,
        string $price,
        string $cost,
        string $printHours,
    ): self {
        return new self(
            $materialId,
            $optionId,
            $variantType,
            $price,
            $cost,
            $printHours
        );
    }

    public function edit(
        VariantType $variantType,
        string $price,
        string $cost,
        string $printHours,
    ): void {
        $this->variantType = $variantType;
        $this->price = $price;
        $this->cost = $cost;
        $this->printHours = $printHours;
    }
}
