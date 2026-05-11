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

    #[Assert\Date]
    public ?string $dateFrom = null;

    #[Assert\Date]
    public ?string $dateTo = null;

    /**
     * @var list<int>
     */
    #[Assert\All([
        new Assert\Positive(),
    ])]
    public array $printIds = [];

    #[Assert\Positive]
    public ?int $materialId = null;

    #[Assert\Positive]
    public ?int $optionId = null;

    #[Assert\Positive]
    public ?int $docs = null;

    #[Assert\Positive]
    public ?int $managerId = null;

    #[Assert\Positive]
    public ?int $designerId = null;

    /**
     * @var list<int>
     */
    #[Assert\All([
        new Assert\Positive(),
    ])]
    public array $statusTypes = [];

    #[Assert\Positive]
    public ?int $storageType = null;

    #[Assert\Positive]
    public ?int $serviceType = null;

    public ?bool $archived = null;

    public ?bool $deleted = null;

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
