<?php

declare(strict_types=1);

namespace App\Modules\Gallery\Entity\Gallery;

use App\Components\Clock\UtcClock;
use App\Modules\Gallery\Entity\Gallery\Fields\Enums\GalleryType;
use DateMalformedStringException;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'Gallery')]
class Gallery
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER, enumType: GalleryType::class)]
    private(set) GalleryType $type;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private(set) string $path;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private(set) ?string $alt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private(set) DateTimeImmutable $createdAt;

    /**
     * @throws DateMalformedStringException
     */
    private function __construct(
        GalleryType $type,
        string $path,
        ?string $alt = null,
    ) {
        $this->type = $type;
        $this->path = $path;
        $this->alt = $alt;
        $this->createdAt = UtcClock::now();
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function create(
        GalleryType $type,
        string $path,
        ?string $alt = null,
    ): self {
        return new self(
            type: $type,
            path: $path,
            alt: $alt,
        );
    }

    public function changeAlt(?string $alt): void
    {
        $this->alt = $alt;
    }
}
