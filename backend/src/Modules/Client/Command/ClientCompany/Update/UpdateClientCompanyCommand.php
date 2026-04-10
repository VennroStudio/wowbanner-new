<?php

declare(strict_types=1);

namespace App\Modules\Client\Command\ClientCompany\Update;

final readonly class UpdateClientCompanyCommand
{
    public function __construct(
        public int $id,
        public string $companyName,
    ) {}
}
