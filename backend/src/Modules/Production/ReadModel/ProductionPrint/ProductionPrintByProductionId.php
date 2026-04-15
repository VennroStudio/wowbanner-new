<?php

declare(strict_types=1);

namespace App\Modules\Production\ReadModel\ProductionPrint;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Production\ReadModel\ProductionPrint\Interface\ProductionPrintModelInterface;
use Override;

final readonly class ProductionPrintByProductionId implements ProductionPrintModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $productionId,
        public int $printId,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     production_id: int,
     *     print_id: int
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: $row['id'],
            productionId: $row['production_id'],
            printId: $row['print_id'],
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
            'id'            => $this->id,
            'production_id' => $this->productionId,
            'print_id'      => $this->printId,
        ];
    }
}
