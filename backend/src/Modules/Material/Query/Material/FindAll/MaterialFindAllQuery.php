<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\Material\FindAll;

use Symfony\Component\Validator\Constraints as Assert;

final class MaterialFindAllQuery
{
    #[Assert\Positive]
    public int $page = 1;

    #[Assert\Positive]
    #[Assert\LessThanOrEqual(100)]
    public int $perPage = 20;

    public ?string $search = null;

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
