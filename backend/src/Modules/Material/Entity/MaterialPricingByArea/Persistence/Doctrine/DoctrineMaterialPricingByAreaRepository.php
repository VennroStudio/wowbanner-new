<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialPricingByArea\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Material\Entity\MaterialPricingByArea\MaterialPricingByArea;
use App\Modules\Material\Entity\MaterialPricingByArea\MaterialPricingByAreaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final class DoctrineMaterialPricingByAreaRepository implements MaterialPricingByAreaRepository
{
    /** @var EntityRepository<MaterialPricingByArea> */
    private EntityRepository $repo;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(MaterialPricingByArea::class);
    }

    #[Override]
    public function add(MaterialPricingByArea $materialPricingByArea): void
    {
        $this->em->persist($materialPricingByArea);
    }

    #[Override]
    public function remove(MaterialPricingByArea $materialPricingByArea): void
    {
        $this->em->remove($materialPricingByArea);
    }

    #[Override]
    public function getById(int $id): MaterialPricingByArea
    {
        if (!$materialPricingByArea = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: 'material',
                message: 'error.material_pricing_by_area_not_found',
                code: 1
            );
        }

        return $materialPricingByArea;
    }

    #[Override]
    public function findById(int $id): ?MaterialPricingByArea
    {
        return $this->repo->findOneBy(['id' => $id]);
    }
}
