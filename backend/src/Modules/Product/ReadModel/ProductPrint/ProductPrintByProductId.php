<?php

declare(strict_types=1);

namespace App\Modules\Product\ReadModel\ProductPrint;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Product\ReadModel\ProductPrint\Interface\ProductPrintModelInterface;
use Override;

final readonly class ProductPrintByProductId implements ProductPrintModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $ProductId,
        public int $printId,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     Product_id: int,
     *     print_id: int
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: $row['id'],
            ProductId: $row['Product_id'],
            printId: $row['print_id'],
        );
    }

    #[Override]
    public function getId(): int
    {
        return $this->id;
    }

    #[Override]
    public function getProductId(): int
    {
        return $this->ProductId;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'Product_id' => $this->ProductId,
            'print_id'      => $this->printId,
        ];
    }
}
