<?php

declare(strict_types=1);

namespace App\Modules\User\Permission;

enum UserPermission: string
{
    case CREATE = 'user.create';
    case UPDATE = 'user.update';
    case DELETE = 'user.delete';
}
