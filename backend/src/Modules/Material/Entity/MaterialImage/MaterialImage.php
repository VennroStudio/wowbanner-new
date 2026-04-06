<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialImage;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'material_images')]
class MaterialImage
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $materialId;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private(set) string $path;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private(set) ?string $alt = null;

    private function __construct(
        int $materialId,
        string $path,
        ?string $alt = null,
    ) {
        $this->materialId = $materialId;
        $this->path = $path;
        $this->alt = $alt;
    }

    public static function create(
        int $materialId,
        string $path,
        ?string $alt = null,
    ): self {
        return new self($materialId, $path, $alt);
    }

    public function edit(?string $alt): void
    {
        $this->alt = $alt;
    }
}
