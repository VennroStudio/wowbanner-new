<?php

declare(strict_types=1);

namespace App\Http\Unifier\Product;

use App\Components\Http\Unifier\UnifierInterface;
use App\Modules\Product\ReadModel\ProductPrint\Interface\ProductPrintModelInterface;
use Override;

final readonly class ProductPrintUnifier implements UnifierInterface
{
    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if (!$item instanceof ProductPrintModelInterface) {
            return [];
        }

        return $this->unify($userId, [$item])[0] ?? [];
    }

    /**
     * @param list<ProductPrintModelInterface> $items
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
        /** @var ProductPrintModelInterface $item */
        return $item->toArray();
    }
}
