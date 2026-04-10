<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\Client\FindByPhone;

final readonly class ClientFindByPhoneQuery
{
    public function __construct(
        public string $phone,
    ) {}
}
