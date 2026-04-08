<?php

declare(strict_types=1);

namespace App\Modules\Processing\ReadModel\ProcessingImage;

use App\Components\Http\Request\RequestFile;
use App\Components\Http\Request\RequestFileItemInterface;
use Override;

final readonly class ProcessingImageItem implements RequestFileItemInterface
{
    public function __construct(
        public RequestFile $file,
        public ?string $alt = null,
    ) {}

    #[Override]
    public static function fromRequest(RequestFile $file, ?string $meta): self
    {
        return new self($file, $meta);
    }
}
