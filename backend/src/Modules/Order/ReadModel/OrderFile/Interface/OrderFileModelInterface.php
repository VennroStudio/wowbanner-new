<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderFile\Interface;

interface OrderFileModelInterface
{
    public function getId(): int;
    public function toArray(): array;
}
