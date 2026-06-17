<?php

declare(strict_types=1);

namespace App\Http\Unifier\Client;

use App\Components\Http\Unifier\UnifierInterface;
use App\Modules\Client\ReadModel\ClientPhone\Interface\ClientPhoneModelInterface;
use Override;

final readonly class ClientPhoneUnifier implements UnifierInterface
{
    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if (!$item instanceof ClientPhoneModelInterface) {
            return [];
        }

        return $this->unify($userId, [$item])[0] ?? [];
    }

    /**
     * @param list<ClientPhoneModelInterface> $items
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
        /** @var ClientPhoneModelInterface $item */
        return $item->toArray();
    }
}
