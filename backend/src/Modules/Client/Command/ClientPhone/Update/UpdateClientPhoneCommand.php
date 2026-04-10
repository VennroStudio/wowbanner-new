<?php

declare(strict_types=1);

namespace App\Modules\Client\Command\ClientPhone\Update;

use App\Modules\Client\Entity\ClientPhone\Fields\PhoneType;

final readonly class UpdateClientPhoneCommand
{
    public function __construct(
        public int $id,
        public PhoneType $type,
        public string $phone,
    ) {}
}
