<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\Material;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Material\ReadModel\Material\Interface\MaterialModelInterface;
use Override;

final readonly class MaterialIdName implements MaterialModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public string $name,
    ) {}

    /**
     * @return array<string, string>
     */
    public static function fields(): array
    {
        return [
            'id'   => 'id',
            'name' => 'name',
        ];
    }

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
