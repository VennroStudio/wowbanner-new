<?php

declare(strict_types=1);

namespace App\Modules\Gallery\Entity\Gallery\Fields\Enums;

use App\Components\Enum\EnumInterface;
use Override;

enum GalleryType: int implements EnumInterface
{
    case MATERIAL = 1;

    #[Override]
    public function getLabel(): string
    {
        return match ($this) {
            self::MATERIAL => 'Материалы',
        };
    }

    public function getPath(int $id): string
    {
        return match ($this) {
            self::MATERIAL => "/gallery/materials/{$id}/",
        };
    }
}
