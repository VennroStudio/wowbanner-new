<?php

declare(strict_types=1);

namespace App\Http\Unifier\Order;

use App\Components\Http\Unifier\UnifierInterface;
use App\Modules\Order\ReadModel\OrderSection\Interface\OrderSectionModelInterface;
use Override;

final readonly class OrderSectionUnifier implements UnifierInterface
{
    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if (!$item instanceof OrderSectionModelInterface) {
            return [];
        }

        return $this->unify($userId, [$item])[0] ?? [];
    }

    /**
     * @param list<object> $items
     * @return list<array<string, mixed>>
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
     */
    #[Override]
    public function map(object $item): array
    {
        return $item->toArray();
    }
}
