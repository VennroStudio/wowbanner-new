<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialProcessing;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Material\ReadModel\MaterialProcessing\Interface\MaterialProcessingModelInterface;
use Override;

final readonly class MaterialProcessingByMaterialIdAndOptionId implements MaterialProcessingModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $materialId,
        public int $optionId,
        public int $processingId,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     material_id: int,
     *     option_id: int,
     *     processing_id: int
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            materialId: (int) $row['material_id'],
            optionId: (int) $row['option_id'],
            processingId: (int) $row['processing_id'],
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
    public function getProcessingId(): int
    {
        return $this->processingId;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'material_id'   => $this->materialId,
            'option_id'     => $this->optionId,
            'processing_id' => $this->processingId,
        ];
    }
}
