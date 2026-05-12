<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialProcessing;

use App\Components\ReadModel\FromRowsTrait;

final readonly class MaterialProcessingGetBySelect
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
            id: (int) $row['id'],
            name: $row['name'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
