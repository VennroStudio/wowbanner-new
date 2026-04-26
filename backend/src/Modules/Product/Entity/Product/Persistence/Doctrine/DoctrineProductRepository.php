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
        $product = $this->findById($id);
        if ($product === null) {
            throw new DomainExceptionModule(
                module: 'Product',
                message: 'error.Product_not_found',
                code: 1
            );
        }

        return $product;
    }

    #[Override]
    public function findById(int $id): ?Product
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    #[Override]
    public function add(Product $product): void
    {
        $this->em->persist($product);
    }

    #[Override]
    public function remove(Product $product): void
    {
        $this->em->remove($product);
    }
}
