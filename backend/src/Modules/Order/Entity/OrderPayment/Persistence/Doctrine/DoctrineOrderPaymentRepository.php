<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderPayment\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Order\Entity\OrderPayment\OrderPayment;
use App\Modules\Order\Entity\OrderPayment\OrderPaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineOrderPaymentRepository implements OrderPaymentRepository
{
    /** @var EntityRepository<OrderPayment> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(OrderPayment::class);
    }

    #[Override]
    public function getById(int $id): OrderPayment
    {
        $orderPayment = $this->findById($id);
        if ($orderPayment === null) {
            throw new DomainExceptionModule(
                module: 'order',
                message: 'error.order_payment_not_found',
                code: 1,
            );
        }

        return $orderPayment;
    }

    #[Override]
    public function findById(int $id): ?OrderPayment
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    /**
     * @return list<OrderPayment>
     */
    #[Override]
    public function findByOrderId(int $orderId): array
    {
        return $this->repo->findBy(['orderId' => $orderId]);
    }

    #[Override]
    public function add(OrderPayment $orderPayment): void
    {
        $this->em->persist($orderPayment);
    }

    #[Override]
    public function remove(OrderPayment $orderPayment): void
    {
        $this->em->remove($orderPayment);
    }
}
