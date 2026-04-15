<?php

declare(strict_types=1);

namespace App\Modules\Product\Entity\ProductPrint;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'Product_prints')]
#[ORM\Index(name: 'idx_Product_id', columns: ['Product_id'])]
#[ORM\Index(name: 'idx_print_id', columns: ['print_id'])]
class ProductPrint
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $ProductId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $printId;

    private function __construct(int $ProductId, int $printId)
    {
        $this->ProductId = $ProductId;
        $this->printId = $printId;
    }

    public static function create(int $ProductId, int $printId): self
    {
        return new self($ProductId, $printId);
    }

    public function edit(int $ProductId, int $printId): void
    {
        $this->ProductId = $ProductId;
        $this->printId = $printId;
    }
}
