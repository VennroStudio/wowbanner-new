<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\Client\Exists;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ClientEmailExistsFetcher
{
    private const string TABLE = 'clients';

    public function __construct(
        private Connection $connection,
    ) {}

    /** @throws Exception */
    public function exists(ClientEmailExistsQuery $query): bool
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('1')
            ->from(self::TABLE)
            ->where('email = :email')
            ->setParameter('email', $query->email)
            ->setMaxResults(1);

        if ($query->excludeClientId !== null) {
            $qb->andWhere('id != :excludeClientId')
                ->setParameter('excludeClientId', $query->excludeClientId);
        }

        return $qb->executeQuery()->fetchOne() !== false;
    }
}
