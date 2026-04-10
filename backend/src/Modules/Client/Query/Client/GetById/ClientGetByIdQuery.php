<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\Client\GetById;

final readonly class ClientGetByIdQuery
{
    public function __construct(
        public int $id,
    ) {}
}
