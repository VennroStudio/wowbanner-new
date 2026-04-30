<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderSection\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Order\Entity\OrderSection\OrderSection;
use App\Modules\Order\Entity\OrderSection\OrderSectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineOrderSectionRepository implements OrderSectionRepository
{
    /** @var EntityRepository<OrderSection> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(OrderSection::class);
    }

    #[Override]
    public function getById(int $id): OrderSection
    {
        $orderSection = $this->findById($id);
        if ($orderSection === null) {
            throw new DomainExceptionModule(
                module: 'order',
                message: 'error.order_section_not_found',
                code: 1
            );
        }

        return $orderSection;
    }

    #[Override]
    public function findById(int $id): ?OrderSection
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    /**
     * @return list<OrderSection>
     */
    #[Override]
    public function findByOrderId(int $orderId): array
    {
        return $this->repo->findBy(['orderId' => $orderId]);
    }

    #[Override]
    public function add(OrderSection $orderSection): void
    {
        $this->em->persist($orderSection);
    }

    #[Override]
    public function remove(OrderSection $orderSection): void
    {
        $this->em->remove($orderSection);
    }
}
