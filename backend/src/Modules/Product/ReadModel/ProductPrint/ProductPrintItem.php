<?php

declare(strict_types=1);

namespace App\Modules\Product\ReadModel\ProductPrint;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProductPrintItem
{
    public function __construct(
        public ?int $id,
        #[Assert\NotBlank(message: 'validation.product_print_id_required')]
        #[Assert\GreaterThan(value: 0, message: 'validation.product_print_id_invalid')]
        public int $printId,
    ) {}
}
