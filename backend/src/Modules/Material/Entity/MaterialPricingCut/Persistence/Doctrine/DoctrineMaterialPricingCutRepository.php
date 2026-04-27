<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialPricingCut\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Material\Entity\MaterialPricingCut\MaterialPricingCut;
use App\Modules\Material\Entity\MaterialPricingCut\MaterialPricingCutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final class DoctrineMaterialPricingCutRepository implements MaterialPricingCutRepository
{
    /** @var EntityRepository<MaterialPricingCut> */
    private EntityRepository $repo;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(MaterialPricingCut::class);
    }

    #[Override]
    public function add(MaterialPricingCut $materialPricingCut): void
    {
        $this->em->persist($materialPricingCut);
    }

    #[Override]
    public function remove(MaterialPricingCut $materialPricingCut): void
    {
        $this->em->remove($materialPricingCut);
    }

    #[Override]
    public function getById(int $id): MaterialPricingCut
    {
        if (!$materialPricingCut = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: 'material',
                message: 'error.material_pricing_cut_not_found',
                code: 1
            );
        }

        return $materialPricingCut;
    }

    #[Override]
    public function findById(int $id): ?MaterialPricingCut
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    #[Override]
    public function findByMaterialId(int $materialId): array
    {
        return $this->repo->findBy(['materialId' => $materialId], ['id' => 'ASC']);
    }
}
