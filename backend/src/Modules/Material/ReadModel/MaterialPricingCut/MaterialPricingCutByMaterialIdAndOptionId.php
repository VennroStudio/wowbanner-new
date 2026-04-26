<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialPricingCut;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Material\Entity\MaterialPricingCut\Fields\Enums\MaterialPricingCutType;
use App\Modules\Material\ReadModel\MaterialPricingCut\Interface\MaterialPricingCutModelInterface;
use Override;

final readonly class MaterialPricingCutByMaterialIdAndOptionId implements MaterialPricingCutModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $materialId,
        public int $optionId,
        public MaterialPricingCutType $type,
        public string $price,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     material_id: int,
     *     option_id: int,
     *     cut_type: int,
     *     price: string|float|int
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            materialId: (int) $row['material_id'],
            optionId: (int) $row['option_id'],
            type: MaterialPricingCutType::from((int) $row['cut_type']),
            price: (string) $row['price'],
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
    public function getOptionId(): int
    {
        return $this->optionId;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'material_id' => $this->materialId,
            'option_id'   => $this->optionId,
            'type'        => ['id' => $this->type->value, 'label' => $this->type->getLabel()],
            'price'       => $this->price,
        ];
    }
}
