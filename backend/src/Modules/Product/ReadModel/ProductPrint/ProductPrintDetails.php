<?php

declare(strict_types=1);

namespace App\Modules\Product\ReadModel\ProductPrint;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Product\ReadModel\ProductPrint\Interface\ProductPrintModelInterface;
use Override;

final readonly class ProductPrintDetails implements ProductPrintModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $productId,
        public int $printId,
        public string $printName,
    ) {}

    /**
     * @return array<string, string>
     */
    public static function fields(): array
    {
        return [
            'id'         => 'id',
            'product_id' => 'product_id',
            'print_id'   => 'print_id',
            'print_name' => 'p.name',
        ];
    }

    /**
     * @param array{
     *     id: int,
     *     product_id: int,
     *     print_id: int,
     *     print_name: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            productId: (int)$row['product_id'],
            printId: (int)$row['print_id'],
            printName: $row['print_name'],
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
        return $this->productId;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'print_id'   => $this->printId,
            'print_name' => $this->printName,
        ];
    }
}
