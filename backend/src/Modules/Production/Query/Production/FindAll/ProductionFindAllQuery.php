<?php

declare(strict_types=1);

namespace App\Modules\Production\Query\Production\FindAll;

use Symfony\Component\Validator\Constraints as Assert;

final class ProductionFindAllQuery
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
