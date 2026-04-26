<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialOption;

use App\Modules\Material\Entity\MaterialOption\Fields\Enums\MaterialOptionPricingType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'material_options')]
#[ORM\Index(name: 'idx_material_option_material_id', columns: ['material_id'])]
class MaterialOption
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private(set) string $name;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $materialId;

    #[ORM\Column(type: Types::INTEGER, enumType: MaterialOptionPricingType::class)]
    private(set) MaterialOptionPricingType $pricingType;

    #[ORM\Column(type: Types::BOOLEAN)]
    private(set) bool $isCut;

    private function __construct(
        string $name,
        int $materialId,
        MaterialOptionPricingType $pricingType,
        bool $isCut,
    ) {
        $this->name = $name;
        $this->materialId = $materialId;
        $this->pricingType = $pricingType;
        $this->isCut = $isCut;
    }

    public static function create(
        string $name,
        int $materialId,
        MaterialOptionPricingType $pricingType,
        bool $isCut,
    ): self {
        return new self($name, $materialId, $pricingType, $isCut);
    }

    public function edit(
        string $name,
        MaterialOptionPricingType $pricingType,
        bool $isCut,
    ): void {
        $this->name = $name;
        $this->pricingType = $pricingType;
        $this->isCut = $isCut;
    }
}
