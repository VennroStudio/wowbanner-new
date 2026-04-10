<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\Client\FindByEmail;

final readonly class ClientFindByEmailQuery
{
    public function __construct(
        public string $email,
    ) {}
}
