<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\Client\Exists;

final readonly class ClientEmailExistsQuery
{
    public function __construct(
        public string $email,
        public ?int $excludeClientId = null,
    ) {}
}
