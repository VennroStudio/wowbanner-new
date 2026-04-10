<?php

declare(strict_types=1);

namespace App\Modules\Client\Query\Client\FindByPhone;

use App\Modules\Client\ReadModel\Client\ClientById;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ClientFindByPhoneFetcher
{
    private const string TABLE = 'client_phones';

    public function __construct(
        private Connection $connection,
    ) {}

    /** @throws Exception */
    public function exists(ClientFindByPhoneQuery $query): bool
    {
        return $this->connection->createQueryBuilder()
            ->select('1')
            ->from(self::TABLE)
            ->where('phone = :phone')
            ->setParameter('phone', $query->phone)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne() !== false;
    }
}
