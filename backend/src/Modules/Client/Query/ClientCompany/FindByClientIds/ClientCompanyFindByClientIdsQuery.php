<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\ClientCompany\FindByClientIds;

final readonly class ClientCompanyFindByClientIdsQuery
{
    /**
     * @param list<int> $clientIds
     */
    public function __construct(
        public array $clientIds,
    ) {}
}
