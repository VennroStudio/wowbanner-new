<?php

declare(strict_types=1);

namespace App\Modules\Product\Entity\ProductMaterial\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Product\Entity\ProductMaterial\ProductMaterial;
use App\Modules\Product\Entity\ProductMaterial\ProductMaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineProductMaterialRepository implements ProductMaterialRepository
{
    /** @var EntityRepository<ProductMaterial> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(ProductMaterial::class);
    }

    #[Override]
    public function getById(int $id): ProductMaterial
    {
        $ProductMaterial = $this->findById($id);
        if ($ProductMaterial === null) {
            throw new DomainExceptionModule(
                module: 'Product',
                message: 'error.Product_material_not_found',
                code: 2
            );
        }

        return $ProductMaterial;
    }

    #[Override]
    public function findById(int $id): ?ProductMaterial
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    /**
     * @return list<ProductMaterial>
     */
    #[Override]
    public function findByProductId(int $ProductId): array
    {
        return $this->repo->findBy(['ProductId' => $ProductId]);
    }

    #[Override]
    public function add(ProductMaterial $ProductMaterial): void
    {
        $this->em->persist($ProductMaterial);
    }

    #[Override]
    public function remove(ProductMaterial $ProductMaterial): void
    {
        $this->em->remove($ProductMaterial);
    }
}
