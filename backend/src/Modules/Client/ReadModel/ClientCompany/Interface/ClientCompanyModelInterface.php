<?php

declare(strict_types=1);

namespace App\Modules\Client\ReadModel\ClientCompany\Interface;

use App\Components\ReadModel\ReadModelInterface;

interface ClientCompanyModelInterface extends ReadModelInterface
{
    public function getClientId(): int;
}
