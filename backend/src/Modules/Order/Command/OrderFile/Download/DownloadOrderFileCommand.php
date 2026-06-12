<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderFile\Download;

final readonly class DownloadOrderFileCommand
{
    public function __construct(
        public int $id,
    ) {}
}
