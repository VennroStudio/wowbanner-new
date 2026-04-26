<?php

declare(strict_types=1);

namespace App\Modules\Product\Entity\ProductPrint\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Product\Entity\ProductPrint\ProductPrint;
use App\Modules\Product\Entity\ProductPrint\ProductPrintRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineProductPrintRepository implements ProductPrintRepository
{
    /** @var EntityRepository<ProductPrint> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(ProductPrint::class);
    }

    #[Override]
    public function getById(int $id): ProductPrint
    {
        $productPrint = $this->findById($id);
        if ($productPrint === null) {
            throw new DomainExceptionModule(
                module: 'Product',
                message: 'error.Product_print_not_found',
                code: 3
            );
        }

        return $productPrint;
    }

    #[Override]
    public function findById(int $id): ?ProductPrint
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    /**
     * @return list<ProductPrint>
     */
    #[Override]
    public function findByProductId(int $productId): array
    {
        return $this->repo->findBy(['productId' => $productId]);
    }

    #[Override]
    public function add(ProductPrint $productPrint): void
    {
        $this->em->persist($productPrint);
    }

    #[Override]
    public function remove(ProductPrint $productPrint): void
    {
        $this->em->remove($productPrint);
    }
}
