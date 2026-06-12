<?php

declare(strict_types=1);

namespace App\Http\Unifier\Order;

use App\Components\Http\Unifier\UnifierHelper;
use App\Components\Http\Unifier\UnifierInterface;
use App\Http\Unifier\Printing\PrintingUnifier;
use App\Modules\Material\Query\Material\GetById\MaterialGetByIdFetcher;
use App\Modules\Material\Query\Material\GetById\MaterialGetByIdQuery;
use App\Modules\Material\Query\MaterialOption\GetById\MaterialOptionGetByIdFetcher;
use App\Modules\Material\Query\MaterialOption\GetById\MaterialOptionGetByIdQuery;
use App\Modules\Order\Query\OrderItemProcessing\FindByItemId\OrderItemProcessingFindByItemIdFetcher;
use App\Modules\Order\Query\OrderItemProcessing\FindByItemId\OrderItemProcessingFindByItemIdQuery;
use App\Modules\Order\ReadModel\OrderItem\Interface\OrderItemModelInterface;
use App\Modules\Order\ReadModel\OrderItem\OrderItemByOrderId;
use App\Modules\Printing\Query\Printing\GetById\PrintingGetByIdFetcher;
use App\Modules\Printing\Query\Printing\GetById\PrintingGetByIdQuery;
use Doctrine\DBAL\Exception;
use Override;

final readonly class OrderItemUnifier implements UnifierInterface
{
    public function __construct(
        private OrderItemProcessingFindByItemIdFetcher $itemProcessingFetcher,
        private OrderItemProcessingUnifier $itemProcessingUnifier,
        private PrintingGetByIdFetcher $printingFetcher,
        private PrintingUnifier $printingUnifier,
        private MaterialGetByIdFetcher $materialFetcher,
        private MaterialOptionGetByIdFetcher $materialOptionFetcher,
    ) {}

    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if (!$item instanceof OrderItemModelInterface) {
            return [];
        }

        return $this->unify($userId, [$item])[0] ?? [];
    }

    /**
     * @param list<object> $items
     * @return list<array<string, mixed>>
     * @throws Exception
     */
    #[Override]
    public function unify(?int $userId, array $items): array
    {
        if ($items === []) {
            return [];
        }

        return array_map($this->map(...), $items);
    }

    /**
     * @return array<string, mixed>
     * @throws Exception
     */
    #[Override]
    public function map(object $item): array
    {
        /** @var OrderItemByOrderId $item */
        $data = UnifierHelper::toArrayWithout($item, 'order_id');
        $processings = $this->itemProcessingFetcher->fetch(new OrderItemProcessingFindByItemIdQuery($item->id));
        $printing = $this->printingFetcher->fetch(new PrintingGetByIdQuery($item->printId));
        $material = $this->materialFetcher->fetch(new MaterialGetByIdQuery($item->materialId));
        $option = $this->materialOptionFetcher->fetch(new MaterialOptionGetByIdQuery($item->optionId));

        $data['processings'] = $this->itemProcessingUnifier->unify(null, $processings);
        $data['print'] = $this->printingUnifier->unifyOne(null, $printing);
        $data['material'] = UnifierHelper::toArrayWithout($material, 'description');
        $data['option'] = UnifierHelper::toArrayWithout($option, 'material_id', 'pricingType', 'isCut');

        return $data;
    }
}
