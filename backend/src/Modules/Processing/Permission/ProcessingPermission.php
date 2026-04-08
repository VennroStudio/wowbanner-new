<?php

declare(strict_types=1);

namespace App\Modules\Processing\Permission;

enum ProcessingPermission: string
{
    case CREATE = 'processing.create';
    case UPDATE = 'processing.update';
    case DELETE = 'processing.delete';
}
