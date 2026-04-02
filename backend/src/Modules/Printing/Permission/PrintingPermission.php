<?php

declare(strict_types=1);

namespace App\Modules\Printing\Permission;

enum PrintingPermission: string
{
    case CREATE = 'printing.create';
    case UPDATE = 'printing.update';
    case DELETE = 'printing.delete';
}
