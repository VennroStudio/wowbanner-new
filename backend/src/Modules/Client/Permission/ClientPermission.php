<?php

declare(strict_types=1);

namespace App\Modules\Client\Permission;

enum ClientPermission: string
{
    case CREATE = 'client.create';
    case UPDATE = 'client.update';
    case DELETE = 'client.delete';
}
