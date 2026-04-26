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
        $productMaterial = $this->findById($id);
        if ($productMaterial === null) {
            throw new DomainExceptionModule(
                module: 'Product',
                message: 'error.Product_material_not_found',
                code: 2
            );
        }

        return $productMaterial;
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
    public function findByProductId(int $productId): array
    {
        return $this->repo->findBy(['productId' => $productId]);
    }

    #[Override]
    public function add(ProductMaterial $productMaterial): void
    {
        $this->em->persist($productMaterial);
    }

    #[Override]
    public function remove(ProductMaterial $productMaterial): void
    {
        $this->em->remove($productMaterial);
    }
}
