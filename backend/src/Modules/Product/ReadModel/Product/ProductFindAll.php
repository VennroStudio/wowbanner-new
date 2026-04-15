<?php

declare(strict_types=1);

namespace App\Modules\Product\ReadModel\Product;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Product\ReadModel\Product\Interface\ProductModelInterface;
use Override;

final readonly class ProductFindAll implements ProductModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public string $name,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     name: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: $row['id'],
            name: $row['name'],
        );
    }

    #[Override]
    public function getId(): int
    {
        return $this->id;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
        ];
    }
}
