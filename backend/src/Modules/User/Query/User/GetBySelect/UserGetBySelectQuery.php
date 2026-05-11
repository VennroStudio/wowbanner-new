<?php

declare(strict_types=1);

namespace App\Modules\User\Query\User\GetBySelect;

use Symfony\Component\Validator\Constraints as Assert;

final class UserGetBySelectQuery
{
    #[Assert\Positive]
    public ?int $role = null;
}
