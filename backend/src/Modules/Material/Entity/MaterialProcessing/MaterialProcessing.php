<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialProcessing;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'material_processings')]
#[ORM\Index(name: 'idx_material_processing_material_id', columns: ['material_id'])]
#[ORM\Index(name: 'idx_material_processing_option_id', columns: ['option_id'])]
#[ORM\Index(name: 'idx_material_processing_processing_id', columns: ['processing_id'])]
class MaterialProcessing
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $materialId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $optionId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $processingId;

    private function __construct(
        int $materialId,
        int $optionId,
        int $processingId,
    ) {
        $this->materialId = $materialId;
        $this->optionId = $optionId;
        $this->processingId = $processingId;
    }

    public static function create(
        int $materialId,
        int $optionId,
        int $processingId,
    ): self {
        return new self($materialId, $optionId, $processingId);
    }

    public function edit(int $processingId): void
    {
        $this->processingId = $processingId;
    }
}
