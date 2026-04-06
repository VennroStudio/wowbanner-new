<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\Material\Create;

final readonly class MaterialImageItem
{
    public function __construct(
        public string $tmpFilePath,
        public ?string $alt = null,
    ) {}
}
