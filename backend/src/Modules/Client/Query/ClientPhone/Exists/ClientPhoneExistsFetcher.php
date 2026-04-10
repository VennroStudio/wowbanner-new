<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\ClientPhone\Exists;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ClientPhoneExistsFetcher
{
    private const string TABLE = 'client_phones';

    public function __construct(
        private Connection $connection,
    ) {}

    /** @throws Exception */
    public function exists(ClientPhoneExistsQuery $query): bool
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('1')
            ->from(self::TABLE)
            ->where('phone = :phone')
            ->setParameter('phone', $query->phone)
            ->setMaxResults(1);

        if ($query->excludeClientId !== null) {
            $qb->andWhere('client_id != :excludeClientId')
                ->setParameter('excludeClientId', $query->excludeClientId);
        }

        return $qb->executeQuery()->fetchOne() !== false;
    }
}
