<?php

declare(strict_types=1);

namespace App\Modules\Client\ReadModel\ClientPhone\Interface;

use App\Components\ReadModel\ReadModelInterface;

interface ClientPhoneModelInterface extends ReadModelInterface
{
    public function getClientId(): int;
}
