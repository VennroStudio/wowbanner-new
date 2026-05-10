<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderSection\Update;

use App\Modules\Order\Entity\OrderSection\Fields\Enums\SectionType;
use App\Modules\Order\Entity\OrderSection\OrderSectionRepository;

final readonly class UpdateOrderSectionHandler
{
    public function __construct(
        private OrderSectionRepository $repository,
    ) {}

    public function handle(UpdateOrderSectionCommand $command): void
    {
        $orderSection = $this->repository->getById($command->id);

        $orderSection->edit(
            sectionType: SectionType::from($command->sectionType),
        );
    }
}
