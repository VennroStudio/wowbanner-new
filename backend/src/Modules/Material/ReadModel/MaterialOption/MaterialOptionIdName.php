<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialOption;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Material\ReadModel\MaterialOption\Interface\MaterialOptionModelInterface;
use Override;

final readonly class MaterialOptionIdName implements MaterialOptionModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public string $name,
        public int $materialId,
    ) {}

    /**
     * @return array<string, string>
     */
    public static function fields(): array
    {
        return [
            'id'          => 'id',
            'name'        => 'name',
            'material_id' => 'material_id',
        ];
    }

    /**
     * @param array{
     *     id: int,
     *     name: string,
     *     material_id: int
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            name: $row['name'],
            materialId: (int) $row['material_id'],
        );
    }

    #[Override]
    public function getId(): int
    {
        return $this->id;
    }

    #[Override]
    public function getMaterialId(): int
    {
        return $this->materialId;
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
