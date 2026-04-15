<?php

declare(strict_types=1);

namespace App\Modules\Product\Entity\Product\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Product\Entity\Product\Product;
use App\Modules\Product\Entity\Product\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineProductRepository implements ProductRepository
{
    /** @var EntityRepository<Product> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(Product::class);
    }

    #[Override]
    public function getById(int $id): Product
    {
        $Product = $this->findById($id);
        if ($Product === null) {
            throw new DomainExceptionModule(
                module: 'Product',
                message: 'error.Product_not_found',
                code: 1
            );
        }

        return $Product;
    }

    #[Override]
    public function findById(int $id): ?Product
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    #[Override]
    public function add(Product $Product): void
    {
        $this->em->persist($Product);
    }

    #[Override]
    public function remove(Product $Product): void
    {
        $this->em->remove($Product);
    }
}
