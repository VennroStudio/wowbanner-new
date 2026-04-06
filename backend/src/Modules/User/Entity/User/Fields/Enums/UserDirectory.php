<?php

declare(strict_types=1);

namespace App\Modules\User\Entity\User\Fields\Enums;

enum UserDirectory
{
    case AVATAR;

    public function getPath(int $id): string
    {
        return match ($this) {
            self::AVATAR => "/users/$id/avatar/",
        };
    }
}
