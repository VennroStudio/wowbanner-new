<?php

declare(strict_types=1);

namespace App\Modules\Processing\Query\Processing\GetById;

final readonly class ProcessingGetByIdQuery
{
    public function __construct(
        public int $id,
    ) {}
}
