<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\Order\FindAll;

use App\Components\ReadModel\ModelCountItemsResult;
use App\Modules\Client\Query\Client\FindAll\ClientFindAllFetcher;
use App\Modules\Order\Query\OrderItem\FindByOrderId\OrderItemFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderItemMilling\FindByOrderId\OrderItemMillingFindByOrderIdFetcher;
use App\Modules\Order\Query\OrderService\FindByOrderId\OrderServiceFindByOrderIdFetcher;
use App\Modules\Order\ReadModel\Order\OrderFindAll;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class OrderFindAllFetcher
{
    private const string TABLE = 'orders';

    public function __construct(
        private Connection $connection,
        private ClientFindAllFetcher $clientFetcher,
        private OrderItemFindByOrderIdFetcher $orderItemFetcher,
        private OrderItemMillingFindByOrderIdFetcher $orderItemMillingFetcher,
        private OrderServiceFindByOrderIdFetcher $orderServiceFetcher,
    ) {}

    /**
     * @return ModelCountItemsResult<OrderFindAll>
     * @throws Exception
     */
    public function fetch(OrderFindAllQuery $query): ModelCountItemsResult
    {
        $qb = $this->connection->createQueryBuilder()
            ->from(self::TABLE, 'o');

        $this->clientFetcher->joinForFilter($qb, 'o');
        $this->orderItemFetcher->joinForFilter($qb, 'o');
        $this->orderItemMillingFetcher->joinForFilter($qb, 'o');
        $this->orderServiceFetcher->joinForFilter($qb, 'o');

        if ($query->archived === true) {
            $qb->andWhere('o.archived_at IS NOT NULL');
        } else {
            $qb->andWhere('o.archived_at IS NULL');
        }

        if ($query->deleted === true) {
            $qb->andWhere('o.deleted_at IS NOT NULL');
        } else {
            $qb->andWhere('o.deleted_at IS NULL');
        }

        if ($query->search !== null && $query->search !== '') {
            $clientAlias = ClientFindAllFetcher::ALIAS;

            $qb->andWhere(
                $qb->expr()->or(
                    "LOWER({$clientAlias}.old_full_name) LIKE LOWER(:search)",
                    "(
                        ({$clientAlias}.old_full_name IS NULL OR {$clientAlias}.old_full_name = '')
                        AND
                        (
                            LOWER({$clientAlias}.last_name) LIKE LOWER(:search)
                            OR LOWER({$clientAlias}.first_name) LIKE LOWER(:search)
                            OR LOWER({$clientAlias}.middle_name) LIKE LOWER(:search)
                        )
                    )"
                )
            )->setParameter('search', '%' . $query->search . '%');
        }

        if ($query->printId !== null) {
            $itemAlias = OrderItemFindByOrderIdFetcher::ALIAS;
            $millingAlias = OrderItemMillingFindByOrderIdFetcher::ALIAS;

            $qb->andWhere(
                $qb->expr()->or(
                    "{$itemAlias}.print_id = :printId",
                    "{$millingAlias}.print_id = :printId"
                )
            )->setParameter('printId', $query->printId);
        }

        if ($query->materialId !== null) {
            $qb->andWhere(OrderItemFindByOrderIdFetcher::ALIAS . '.material_id = :materialId')
                ->setParameter('materialId', $query->materialId);
        }

        if ($query->optionId !== null) {
            $qb->andWhere(OrderItemFindByOrderIdFetcher::ALIAS . '.option_id = :optionId')
                ->setParameter('optionId', $query->optionId);
        }

        if ($query->managerId !== null) {
            $qb->andWhere('o.manager_id = :managerId')
                ->setParameter('managerId', $query->managerId);
        }

        if ($query->designerId !== null) {
            $qb->andWhere('o.designer_id = :designerId')
                ->setParameter('designerId', $query->designerId);
        }

        if ($query->statusType !== null) {
            $qb->andWhere('o.status_type = :statusType')
                ->setParameter('statusType', $query->statusType);
        }

        if ($query->storageType !== null) {
            $qb->andWhere('o.storage_type = :storageType')
                ->setParameter('storageType', $query->storageType);
        }

        if ($query->serviceType !== null) {
            $qb->andWhere(OrderServiceFindByOrderIdFetcher::ALIAS . '.service_type = :serviceType')
                ->setParameter('serviceType', $query->serviceType);
        }

        $countQb = clone $qb;
        $total = (int) $countQb->select('COUNT(DISTINCT o.id)')->executeQuery()->fetchOne();

        $qb->groupBy('o.id');

        $rows = $qb->select(
            'o.id',
            'o.creator_id',
            'o.manager_id',
            'o.designer_id',
            'o.client_id',
            'o.status_type',
            'o.storage_type',
            'o.general_note',
            'o.extension',
            'o.created_at',
            'o.accepted_at',
            'o.deadline_at'
        )
            ->orderBy('o.id', 'DESC')
            ->setFirstResult($query->getOffset())
            ->setMaxResults($query->perPage)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<OrderFindAll> $items */
        $items = OrderFindAll::fromRows($rows);

        return new ModelCountItemsResult(
            items: $items,
            count: $total,
        );
    }
}
