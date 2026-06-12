<?php

declare(strict_types=1);

namespace App\Http\Unifier\Order;

use App\Components\Http\Unifier\UnifierInterface;
use App\Http\Unifier\Printing\PrintingUnifier;
use App\Modules\Order\ReadModel\OrderItemMilling\Interface\OrderItemMillingModelInterface;
use App\Modules\Order\ReadModel\OrderItemMilling\OrderItemMillingDetails;
use App\Modules\Printing\Query\Printing\GetById\PrintingGetByIdFetcher;
use App\Modules\Printing\Query\Printing\GetById\PrintingGetByIdQuery;
use Doctrine\DBAL\Exception;
use Override;

final readonly class OrderItemMillingUnifier implements UnifierInterface
{
    public function __construct(
        private PrintingGetByIdFetcher $printingFetcher,
        private PrintingUnifier $printingUnifier,
    ) {}

    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if (!$item instanceof OrderItemMillingModelInterface) {
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
        /** @var OrderItemMillingDetails $item */
        $data = $item->toArray();
        $printing = $this->printingFetcher->fetch(new PrintingGetByIdQuery($item->printId));

        $data['print'] = $this->printingUnifier->unifyOne(null, $printing);

        return $data;
    }
}
