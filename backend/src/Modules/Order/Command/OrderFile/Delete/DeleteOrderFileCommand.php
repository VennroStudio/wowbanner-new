<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderFile\Delete;

final readonly class DeleteOrderFileCommand
{
    public function __construct(
        public int $id,
    ) {}
}
