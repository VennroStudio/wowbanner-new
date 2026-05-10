<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\Order\FindAll;

use Symfony\Component\Validator\Constraints as Assert;

final class OrderFindAllQuery
{
    #[Assert\Positive]
    public int $page = 1;

    #[Assert\Positive]
    #[Assert\LessThanOrEqual(100)]
    public int $perPage = 20;

    public ?string $search = null;

    #[Assert\Positive]
    public ?int $printId = null;

    #[Assert\Positive]
    public ?int $materialId = null;

    #[Assert\Positive]
    public ?int $optionId = null;

    #[Assert\Positive]
    public ?int $managerId = null;

    #[Assert\Positive]
    public ?int $designerId = null;

    #[Assert\Positive]
    public ?int $statusType = null;

    public ?bool $archived = null;

    public ?bool $deleted = null;

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
