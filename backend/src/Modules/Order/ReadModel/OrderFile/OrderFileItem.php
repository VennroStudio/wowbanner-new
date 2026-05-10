<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderFile;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class OrderFileItem
{
    public function __construct(
        public ?int $id,
        #[Assert\NotBlank(message: 'validation.order_file_tmp_file_path_required')]
        public string $tmpFilePath,
        #[Assert\NotBlank(message: 'validation.order_file_original_name_required')]
        public string $originalName,
    ) {}
}
