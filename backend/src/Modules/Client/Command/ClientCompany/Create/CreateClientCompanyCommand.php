<?php

declare(strict_types=1);

namespace App\Modules\Client\Command\ClientCompany\Create;

final readonly class CreateClientCompanyCommand
{
    public function __construct(
        public int $clientId,
        public string $companyName,
    ) {}
}
