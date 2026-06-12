<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderFile\Download;

final readonly class DownloadOrderFileResult
{
    public function __construct(
        public string $fileName,
        public string $contentType,
        public string $content,
    ) {}
}
