<?php

declare(strict_types=1);

namespace App\Modules\Production\Entity\ProductionPrint\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Production\Entity\ProductionPrint\ProductionPrint;
use App\Modules\Production\Entity\ProductionPrint\ProductionPrintRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineProductionPrintRepository implements ProductionPrintRepository
{
    /** @var EntityRepository<ProductionPrint> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(ProductionPrint::class);
    }

    #[Override]
    public function getById(int $id): ProductionPrint
    {
        $productionPrint = $this->findById($id);
        if ($productionPrint === null) {
            throw new DomainExceptionModule(
                module: 'production',
                message: 'error.production_print_not_found',
                code: 3
            );
        }

        return $productionPrint;
    }

    #[Override]
    public function findById(int $id): ?ProductionPrint
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    /**
     * @return list<ProductionPrint>
     */
    #[Override]
    public function findByProductionId(int $productionId): array
    {
        return $this->repo->findBy(['productionId' => $productionId]);
    }

    #[Override]
    public function add(ProductionPrint $productionPrint): void
    {
        $this->em->persist($productionPrint);
    }

    #[Override]
    public function remove(ProductionPrint $productionPrint): void
    {
        $this->em->remove($productionPrint);
    }
}
