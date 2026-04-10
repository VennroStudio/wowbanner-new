<?php

declare(strict_types=1);

namespace App\Modules\Client\Command\ClientPhone\Create;

use App\Modules\Client\Entity\ClientPhone\Fields\PhoneType;

final readonly class CreateClientPhoneCommand
{
    public function __construct(
        public int $clientId,
        public PhoneType $type,
        public string $phone,
    ) {}
}
