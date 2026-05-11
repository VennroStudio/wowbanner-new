<?php

declare(strict_types=1);

namespace App\Modules\User\Query\User\GetBySelect;

use App\Modules\User\ReadModel\User\UserGetBySelect;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class UserGetBySelectFetcher
{
    private const string TABLE = 'users';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return list<UserGetBySelect>
     * @throws Exception
     */
    public function fetch(UserGetBySelectQuery $query): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'role',
                'status',
                'first_name',
                'last_name',
                'email',
            )
            ->from(self::TABLE)
            ->where('deleted_at IS NULL')
            ->orderBy('last_name', 'ASC')
            ->addOrderBy('first_name', 'ASC');

        if ($query->role !== null) {
            $qb->andWhere('role = :role')
                ->setParameter('role', $query->role);
        }

        $rows = $qb->executeQuery()->fetchAllAssociative();

        return UserGetBySelect::fromRows($rows);
    }
}
