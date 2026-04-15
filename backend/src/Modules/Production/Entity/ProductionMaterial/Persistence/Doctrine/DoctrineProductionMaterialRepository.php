<?php

declare(strict_types=1);

namespace App\Modules\Production\Entity\ProductionMaterial\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Production\Entity\ProductionMaterial\ProductionMaterial;
use App\Modules\Production\Entity\ProductionMaterial\ProductionMaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineProductionMaterialRepository implements ProductionMaterialRepository
{
    /** @var EntityRepository<ProductionMaterial> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(ProductionMaterial::class);
    }

    #[Override]
    public function getById(int $id): ProductionMaterial
    {
        $productionMaterial = $this->findById($id);
        if ($productionMaterial === null) {
            throw new DomainExceptionModule(
                module: 'production',
                message: 'error.production_material_not_found',
                code: 2
            );
        }

        return $productionMaterial;
    }

    #[Override]
    public function findById(int $id): ?ProductionMaterial
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    /**
     * @return list<ProductionMaterial>
     */
    #[Override]
    public function findByProductionId(int $productionId): array
    {
        return $this->repo->findBy(['productionId' => $productionId]);
    }

    #[Override]
    public function add(ProductionMaterial $productionMaterial): void
    {
        $this->em->persist($productionMaterial);
    }

    #[Override]
    public function remove(ProductionMaterial $productionMaterial): void
    {
        $this->em->remove($productionMaterial);
    }
}
