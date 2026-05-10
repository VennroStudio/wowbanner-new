<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderSection\Update;

final readonly class UpdateOrderSectionCommand
{
    public function __construct(
        public int $id,
        public int $sectionType,
    ) {}
}
