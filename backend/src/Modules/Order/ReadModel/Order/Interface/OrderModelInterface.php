<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\Order\Interface;

interface OrderModelInterface
{
    public function getId(): int;
    public function getClientId(): int;
    public function getManagerId(): ?int;
    public function getDesignerId(): ?int;
    public function toArray(): array;
}
