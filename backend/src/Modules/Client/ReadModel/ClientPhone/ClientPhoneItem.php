<?php

declare(strict_types=1);

namespace App\Modules\Client\ReadModel\ClientPhone;

final readonly class ClientPhoneItem
{
    public function __construct(
        public ?int $id,
        public int $type,
        public string $phone,
    ) {}
}
