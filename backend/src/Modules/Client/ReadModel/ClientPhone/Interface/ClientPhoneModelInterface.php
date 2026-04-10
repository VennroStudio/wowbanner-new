<?php

declare(strict_types=1);

namespace App\Modules\Client\ReadModel\ClientPhone\Interface;

interface ClientPhoneModelInterface
{
    public function getId(): int;
    public function toArray(): array;
}
