<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialOption;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Material\Entity\MaterialOption\Fields\Enums\MaterialOptionPricingType;
use App\Modules\Material\ReadModel\MaterialOption\Interface\MaterialOptionModelInterface;
use Override;

final readonly class MaterialOptionById implements MaterialOptionModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public string $name,
        public int $materialId,
        public MaterialOptionPricingType $pricingType,
        public bool $isCut,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     name: string,
     *     material_id: int,
     *     pricing_type: int,
     *     is_cut: int|string|bool
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            name: (string) $row['name'],
            materialId: (int) $row['material_id'],
            pricingType: MaterialOptionPricingType::from((int) $row['pricing_type']),
            isCut: (bool) (int) $row['is_cut'],
        );
    }

    #[Override]
    public function getId(): int
    {
        return $this->id;
    }

    #[Override]
    public function getMaterialId(): int
    {
        return $this->materialId;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'material_id'  => $this->materialId,
            'pricing_type' => [
                'id'    => $this->pricingType->value,
                'label' => $this->pricingType->getLabel(),
            ],
            'is_cut'       => $this->isCut,
        ];
    }
}
