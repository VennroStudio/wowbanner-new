<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderDelivery;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class OrderDeliveryItem
{
    public function __construct(
        public ?int $id,
        #[Assert\NotBlank(message: 'validation.order_delivery_type_required')]
        public int $deliveryType,
        public ?string $address = null,
        public ?string $comment = null,
    ) {}
}
