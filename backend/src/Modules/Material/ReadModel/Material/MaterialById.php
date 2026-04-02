<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\Material;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Material\ReadModel\Material\Interface\MaterialModelInterface;
use Override;

final readonly class MaterialById implements MaterialModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public string $name,
        public string $description,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     name: string,
     *     description: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: $row['id'],
            name: $row['name'],
            description: $row['description'],
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
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
        ];
    }
}
