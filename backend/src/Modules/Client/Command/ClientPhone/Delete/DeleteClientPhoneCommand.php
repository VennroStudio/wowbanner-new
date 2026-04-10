<?php

declare(strict_types=1);

namespace App\Modules\Client\Command\ClientPhone\Delete;

final readonly class DeleteClientPhoneCommand
{
    public function __construct(
        public int $id,
    ) {}
}
