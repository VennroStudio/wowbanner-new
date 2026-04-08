<?php

declare(strict_types=1);

namespace App\Modules\Processing\Entity\ProcessingImage;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'processing_images')]
class ProcessingImage
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $processingId;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private(set) string $path;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private(set) ?string $alt = null;

    private function __construct(
        int $processingId,
        string $path,
        ?string $alt = null,
    ) {
        $this->processingId = $processingId;
        $this->path = $path;
        $this->alt = $alt;
    }

    public static function create(
        int $processingId,
        string $path,
        ?string $alt = null,
    ): self {
        return new self($processingId, $path, $alt);
    }

    public function edit(?string $path, ?string $alt): void
    {
        if ($path !== null) {
            $this->path = $path;
        }
        $this->alt = $alt;
    }
}
