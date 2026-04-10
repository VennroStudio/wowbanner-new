<?php

declare(strict_types=1);

namespace App\Modules\Client\ReadModel\ClientCompany;

final readonly class ClientCompanyItem
{
    public function __construct(
        public ?int $id,
        public string $name,
    ) {}
}
