<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\ClientPhone\FindByClientIds;

final readonly class ClientPhoneFindByClientIdsQuery
{
    /**
     * @param list<int> $clientIds
     */
    public function __construct(
        public array $clientIds,
    ) {}
}
