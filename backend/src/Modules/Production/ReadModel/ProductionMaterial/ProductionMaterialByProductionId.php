<?php

declare(strict_types=1);

namespace App\Modules\Production\ReadModel\ProductionMaterial;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Production\ReadModel\ProductionMaterial\Interface\ProductionMaterialModelInterface;
use Override;

final readonly class ProductionMaterialByProductionId implements ProductionMaterialModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $productionId,
        public int $materialOptionId,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     production_id: int,
     *     material_option_id: int
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: $row['id'],
            productionId: $row['production_id'],
            materialOptionId: $row['material_option_id'],
        );
    }

    #[Override]
    public function getId(): int
    {
        return $this->id;
    }

    #[Override]
    public function getProductionId(): int
    {
        return $this->productionId;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'                 => $this->id,
            'production_id'      => $this->productionId,
            'material_option_id' => $this->materialOptionId,
        ];
    }
}
