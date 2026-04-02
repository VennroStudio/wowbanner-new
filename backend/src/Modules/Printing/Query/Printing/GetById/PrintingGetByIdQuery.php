<?php

declare(strict_types=1);

namespace App\Modules\Printing\Query\Printing\GetById;

final readonly class PrintingGetByIdQuery
{
    public function __construct(
        public int $id,
    ) {}
}
