<?php

declare(strict_types=1);

namespace App\Modules\Production\Entity\ProductionPrint;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'production_prints')]
#[ORM\Index(columns: ['production_id'], name: 'idx_production_id')]
#[ORM\Index(columns: ['print_id'], name: 'idx_print_id')]
class ProductionPrint
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $productionId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $printId;

    private function __construct(int $productionId, int $printId)
    {
        $this->productionId = $productionId;
        $this->printId = $printId;
    }

    public static function create(int $productionId, int $printId): self
    {
        return new self($productionId, $printId);
    }

    public function edit(int $productionId, int $printId): void
    {
        $this->productionId = $productionId;
        $this->printId = $printId;
    }
}
