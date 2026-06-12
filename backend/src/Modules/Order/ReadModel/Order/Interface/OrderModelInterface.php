<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\Order\Interface;

use App\Components\ReadModel\ReadModelInterface;

interface OrderModelInterface extends ReadModelInterface
{
    public function getClientId(): int;

    public function getManagerId(): ?int;

    public function getDesignerId(): ?int;
}
