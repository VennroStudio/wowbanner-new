<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\Client\FindByEmail;

use App\Modules\Client\ReadModel\Client\ClientById;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ClientFindByEmailFetcher
{
    private const string TABLE = 'clients';

    public function __construct(
        private Connection $connection,
    ) {}

    /** @throws Exception */
    public function exists(ClientFindByEmailQuery $query): bool
    {
        return $this->connection->createQueryBuilder()
            ->select('1')
            ->from(self::TABLE)
            ->where('email = :email')
            ->setParameter('email', $query->email)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne() !== false;
    }
}
