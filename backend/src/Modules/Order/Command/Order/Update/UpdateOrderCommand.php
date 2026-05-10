<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\Order\Update;

use DateTimeImmutable;
use App\Modules\Order\ReadModel\OrderDelivery\OrderDeliveryItem;
use App\Modules\Order\ReadModel\OrderFile\OrderFileItem;
use App\Modules\Order\ReadModel\OrderItem\OrderItemItem;
use App\Modules\Order\ReadModel\OrderItemMilling\OrderItemMillingItem;
use App\Modules\Order\ReadModel\OrderPayment\OrderPaymentItem;
use App\Modules\Order\ReadModel\OrderSection\OrderSectionItem;
use App\Modules\Order\ReadModel\OrderService\OrderServiceItem;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateOrderCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $currentUserId,

        #[Assert\NotBlank]
        public int $currentUserRole,

        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $id,

        public ?int $managerId,

        public ?int $designerId,

        #[Assert\NotBlank(message: 'validation.order_client_id_required')]
        #[Assert\GreaterThan(0)]
        public int $clientId,

        #[Assert\NotBlank(message: 'validation.order_status_type_required')]
        public int $statusType,

        #[Assert\NotBlank(message: 'validation.order_storage_type_required')]
        public int $storageType,

        #[Assert\NotNull(message: 'validation.order_accepted_at_required')]
        public DateTimeImmutable $acceptedAt,

        #[Assert\NotNull(message: 'validation.order_deadline_at_required')]
        public DateTimeImmutable $deadlineAt,

        public ?string $generalNote = null,

        public ?string $extension = null,

        public ?OrderDeliveryItem $delivery = null,

        /** @var list<OrderFileItem> */
        #[Assert\Valid]
        public array $files = [],

        /** @var list<OrderItemItem> */
        #[Assert\Valid]
        public array $items = [],

        /** @var list<OrderItemMillingItem> */
        #[Assert\Valid]
        public array $millings = [],

        /** @var list<OrderPaymentItem> */
        #[Assert\Valid]
        public array $payments = [],

        /** @var list<OrderSectionItem> */
        #[Assert\Valid]
        public array $sections = [],

        /** @var list<OrderServiceItem> */
        #[Assert\Valid]
        public array $services = [],
    ) {}
}
