<?php

declare(strict_types=1);

namespace App\Modules\Production\Permission;

enum ProductionPermission: string
{
    case CREATE = 'production.create';
    case UPDATE = 'production.update';
    case DELETE = 'production.delete';
}
