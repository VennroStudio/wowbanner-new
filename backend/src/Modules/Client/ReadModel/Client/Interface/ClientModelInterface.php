<?php

declare(strict_types=1);

namespace App\Modules\Client\ReadModel\Client\Interface;

interface ClientModelInterface
{
    public function getId(): int;
    public function toArray(): array;
}
