<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderFile\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Order\Entity\OrderFile\OrderFile;
use App\Modules\Order\Entity\OrderFile\OrderFileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineOrderFileRepository implements OrderFileRepository
{
    /** @var EntityRepository<OrderFile> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(OrderFile::class);
    }

    #[Override]
    public function getById(int $id): OrderFile
    {
        $orderFile = $this->findById($id);
        if ($orderFile === null) {
            throw new DomainExceptionModule(
                module: 'order',
                message: 'error.order_file_not_found',
                code: 1,
            );
        }

        return $orderFile;
    }

    #[Override]
    public function findById(int $id): ?OrderFile
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    /**
     * @return list<OrderFile>
     */
    #[Override]
    public function findByOrderId(int $orderId): array
    {
        return $this->repo->findBy(['orderId' => $orderId]);
    }

    #[Override]
    public function add(OrderFile $orderFile): void
    {
        $this->em->persist($orderFile);
    }

    #[Override]
    public function remove(OrderFile $orderFile): void
    {
        $this->em->remove($orderFile);
    }
}
