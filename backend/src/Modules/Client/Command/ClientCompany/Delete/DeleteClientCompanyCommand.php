<?php

declare(strict_types=1);

namespace App\Modules\Client\Command\ClientCompany\Delete;

final readonly class DeleteClientCompanyCommand
{
    public function __construct(
        public int $id,
    ) {}
}
