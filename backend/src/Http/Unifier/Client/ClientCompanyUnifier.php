<?php

declare(strict_types=1);

namespace App\Http\Unifier\Client;

use App\Components\Http\Unifier\UnifierInterface;
use App\Modules\Client\ReadModel\ClientCompany\Interface\ClientCompanyModelInterface;
use Override;

final readonly class ClientCompanyUnifier implements UnifierInterface
{
    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if (!$item instanceof ClientCompanyModelInterface) {
            return [];
        }

        return $this->unify($userId, [$item])[0] ?? [];
    }

    /**
     * @param list<ClientCompanyModelInterface> $items
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
        /** @var ClientCompanyModelInterface $item */
        return $item->toArray();
    }
}
