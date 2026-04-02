<?php

declare(strict_types=1);

namespace App\Modules\Material\Permission;

enum MaterialPermission: string
{
    case CREATE = 'material.create';
    case UPDATE = 'material.update';
    case DELETE = 'material.delete';
}
