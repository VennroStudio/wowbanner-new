<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderService;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class OrderServiceItem
{
    public function __construct(
        public ?int $id,
        #[Assert\NotBlank(message: 'validation.order_service_type_required')]
        public int $serviceType,
        #[Assert\NotBlank(message: 'validation.order_service_price_required')]
        public string $price,
        public ?string $note = null,
    ) {}
}
