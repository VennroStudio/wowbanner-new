<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderSection;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class OrderSectionItem
{
    public function __construct(
        public ?int $id,
        #[Assert\NotBlank(message: 'validation.order_section_type_required')]
        public int $sectionType,
    ) {}
}
