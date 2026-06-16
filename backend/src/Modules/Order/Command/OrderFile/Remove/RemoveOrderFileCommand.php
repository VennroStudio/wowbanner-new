<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderFile\Remove;

final readonly class RemoveOrderFileCommand
{
    public function __construct(
        public int $id,
    ) {}
}
