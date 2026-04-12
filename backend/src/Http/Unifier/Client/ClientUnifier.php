<?php

declare(strict_types=1);

namespace App\Http\Unifier\Client;

use App\Components\Http\Unifier\UnifierHelper;
use App\Components\Http\Unifier\UnifierInterface;
use App\Modules\Client\Query\ClientCompany\FindByClientIds\ClientCompanyFindByClientIdsFetcher;
use App\Modules\Client\Query\ClientCompany\FindByClientIds\ClientCompanyFindByClientIdsQuery;
use App\Modules\Client\Query\ClientPhone\FindByClientIds\ClientPhoneFindByClientIdsFetcher;
use App\Modules\Client\Query\ClientPhone\FindByClientIds\ClientPhoneFindByClientIdsQuery;
use App\Modules\Client\ReadModel\Client\Interface\ClientModelInterface;
use App\Modules\Client\ReadModel\ClientCompany\ClientCompanyByClient;
use App\Modules\Client\ReadModel\ClientPhone\ClientPhoneByClient;
use Doctrine\DBAL\Exception;
use Override;

final readonly class ClientUnifier implements UnifierInterface
{
    public function __construct(
        private ClientPhoneFindByClientIdsFetcher $phoneFetcher,
        private ClientCompanyFindByClientIdsFetcher $companyFetcher,
    ) {}

    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if ($item === null) {
            return [];
        }
        return $this->unify($userId, [$item])[0] ?? [];
    }

    /**
     * @param list<ClientModelInterface> $items
     * @return list<array<string, mixed>>
     * @throws Exception
     */
    #[Override]
    public function unify(?int $userId, array $items): array
    {
        if ($items === []) {
            return [];
        }

        $ids = array_map(static fn(ClientModelInterface $i): int => $i->getId(), $items);

        $phones = $this->groupPhones($this->phoneFetcher->fetch(new ClientPhoneFindByClientIdsQuery($ids)));
        $companies = $this->groupCompanies($this->companyFetcher->fetch(new ClientCompanyFindByClientIdsQuery($ids)));

        return array_map(
            fn(ClientModelInterface $item): array => $this->map($item, $phones, $companies),
            $items
        );
    }

    /**
     * @param array<int, list<array<string, mixed>>> $phones
     * @param array<int, list<array<string, mixed>>> $companies
     */
    #[Override]
    public function map(object $item, array $phones = [], array $companies = []): array
    {
        /** @var ClientModelInterface $item */
        $data = $item->toArray();
        $data['phones'] = $phones[$item->getId()] ?? [];
        $data['companies'] = $companies[$item->getId()] ?? [];
        return UnifierHelper::withTimestamps($data, $item);
    }

    /**
     * @param list<ClientPhoneByClient> $items
     * @return array<int, list<array<string, mixed>>>
     */
    private function groupPhones(array $items): array
    {
        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item->clientId][] = UnifierHelper::toArrayWithout($item, 'client_id');
        }
        return $grouped;
    }

    /**
     * @param list<ClientCompanyByClient> $items
     * @return array<int, list<array<string, mixed>>>
     */
    private function groupCompanies(array $items): array
    {
        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item->clientId][] = UnifierHelper::toArrayWithout($item, 'client_id');
        }
        return $grouped;
    }
}
