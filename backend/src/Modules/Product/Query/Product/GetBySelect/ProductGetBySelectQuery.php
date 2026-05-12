<?php

declare(strict_types=1);

namespace App\Modules\Product\Query\Product\GetBySelect;

use Symfony\Component\Validator\Constraints as Assert;

final class ProductGetBySelectQuery
{
    #[Assert\Positive]
    public ?int $printId = null;
}
