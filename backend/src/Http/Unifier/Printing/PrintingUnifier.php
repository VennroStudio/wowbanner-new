<?php

declare(strict_types=1);

namespace App\Http\Unifier\Printing;

use App\Components\Http\Unifier\UnifierInterface;
use App\Modules\Printing\ReadModel\Printing\Interface\PrintingModelInterface;
use Override;

final readonly class PrintingUnifier implements UnifierInterface
{
    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if (!$item instanceof PrintingModelInterface) {
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

        /** @var list<array<string, mixed>> */
        return array_map($this->map(...), $items);
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function map(object $item): array
    {
        /** @var PrintingModelInterface $item */
        return $item->toArray();
    }
}
