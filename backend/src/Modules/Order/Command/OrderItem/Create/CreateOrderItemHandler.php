<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderItem\Create;

use App\Modules\Material\Entity\MaterialPricingByArea\Fields\Enums\DpiType;
use App\Modules\Material\Entity\MaterialPricingByPiece\Fields\Enums\VariantType;
use App\Modules\Order\Entity\OrderItem\OrderItem;
use App\Modules\Order\Entity\OrderItem\OrderItemRepository;

final readonly class CreateOrderItemHandler
{
    public function __construct(
        private OrderItemRepository $repository,
    ) {}

    public function handle(CreateOrderItemCommand $command): OrderItem
    {
        $orderItem = OrderItem::create(
            orderId: $command->orderId,
            sourceItemId: $command->sourceItemId,
            printId: $command->printId,
            productId: $command->productId,
            materialId: $command->materialId,
            optionId: $command->optionId,
            dpiType: DpiType::from($command->dpiType),
            variantType: VariantType::from($command->variantType),
            width: $command->width,
            height: $command->height,
            quantity: $command->quantity,
            performerId: $command->performerId,
            note: $command->note,
            price: $command->price,
            printed: $command->printed,
            ready: $command->ready,
        );

        $this->repository->add($orderItem);

        return $orderItem;
    }
}
