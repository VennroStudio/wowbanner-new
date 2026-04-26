<?php

declare(strict_types=1);

namespace App\Modules\Product\Entity\ProductPrint;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'product_prints')]
#[ORM\Index(name: 'idx_product_id', columns: ['product_id'])]
#[ORM\Index(name: 'idx_print_id', columns: ['print_id'])]
class ProductPrint
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $productId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $printId;

    private function __construct(int $productId, int $printId)
    {
        $this->productId = $productId;
        $this->printId = $printId;
    }

    public static function create(int $productId, int $printId): self
    {
        return new self($productId, $printId);
    }

    public function edit(int $productId, int $printId): void
    {
        $this->productId = $productId;
        $this->printId = $printId;
    }
}
