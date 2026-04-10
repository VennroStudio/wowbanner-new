<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\ClientPhone\Exists;

final readonly class ClientPhoneExistsQuery
{
    public function __construct(
        public string $phone,
        public ?int $excludeClientId = null,
    ) {}
}
