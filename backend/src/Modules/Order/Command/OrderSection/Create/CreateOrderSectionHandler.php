<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderSection\Create;

use App\Modules\Order\Entity\OrderSection\Fields\Enums\SectionType;
use App\Modules\Order\Entity\OrderSection\OrderSection;
use App\Modules\Order\Entity\OrderSection\OrderSectionRepository;

final readonly class CreateOrderSectionHandler
{
    public function __construct(
        private OrderSectionRepository $repository,
    ) {}

    public function handle(CreateOrderSectionCommand $command): void
    {
        $orderSection = OrderSection::create(
            orderId: $command->orderId,
            sectionType: SectionType::from($command->sectionType),
        );

        $this->repository->add($orderSection);
    }
}
