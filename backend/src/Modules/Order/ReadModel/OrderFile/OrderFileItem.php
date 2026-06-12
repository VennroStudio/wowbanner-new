<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderFile;

use App\Components\Http\Request\RequestFile;
use App\Components\Http\Request\RequestFileItemInterface;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class OrderFileItem implements RequestFileItemInterface
{
    public function __construct(
        public ?int $id,
        public RequestFile $file,
        #[Assert\NotBlank(message: 'validation.order_file_tmp_file_path_required')]
        public string $tmpFilePath,
        #[Assert\NotBlank(message: 'validation.order_file_original_name_required')]
        public string $originalName,
    ) {}

    #[Override]
    public static function fromRequest(RequestFile $file, ?string $meta): self
    {
        $originalName = $meta;

        if ($originalName === null || $originalName === '') {
            $originalName = (string)($file->getOriginalFile()->getClientFilename() ?? basename($file->getPath()));
        }

        return new self(
            id: null,
            file: $file,
            tmpFilePath: $file->getPath(),
            originalName: $originalName,
        );
    }
}
