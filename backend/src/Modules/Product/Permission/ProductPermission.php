<?php

declare(strict_types=1);

namespace App\Modules\Product\Permission;

enum ProductPermission: string
{
    case CREATE = 'Product.create';
    case UPDATE = 'Product.update';
    case DELETE = 'Product.delete';
}
