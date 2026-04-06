<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialImage;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Material\ReadModel\MaterialImage\Interface\MaterialImageModelInterface;
use Override;

final readonly class MaterialImageByMaterial implements MaterialImageModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $materialId,
        public string $path,
        public ?string $alt,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     material_id: int,
     *     path: string,
     *     alt: string|null
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: $row['id'],
            materialId: $row['material_id'],
            path: $row['path'],
            alt: $row['alt'],
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
            'id'          => $this->id,
            'material_id' => $this->materialId,
            'path'        => $this->path,
            'alt'         => $this->alt,
        ];
    }
}
