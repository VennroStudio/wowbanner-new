<?php

declare(strict_types=1);

namespace App\Modules\Client\ReadModel\ClientCompany\Interface;

interface ClientCompanyModelInterface
{
    public function getId(): int;
    public function toArray(): array;
}
