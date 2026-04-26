<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialPricingByPiece\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Material\Entity\MaterialPricingByPiece\MaterialPricingByPiece;
use App\Modules\Material\Entity\MaterialPricingByPiece\MaterialPricingByPieceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final class DoctrineMaterialPricingByPieceRepository implements MaterialPricingByPieceRepository
{
    /** @var EntityRepository<MaterialPricingByPiece> */
    private EntityRepository $repo;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(MaterialPricingByPiece::class);
    }

    #[Override]
    public function add(MaterialPricingByPiece $materialPricingByPiece): void
    {
        $this->em->persist($materialPricingByPiece);
    }

    #[Override]
    public function remove(MaterialPricingByPiece $materialPricingByPiece): void
    {
        $this->em->remove($materialPricingByPiece);
    }

    #[Override]
    public function getById(int $id): MaterialPricingByPiece
    {
        if (!$materialPricingByPiece = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: 'material',
                message: 'error.material_pricing_by_piece_not_found',
                code: 1
            );
        }

        return $materialPricingByPiece;
    }

    #[Override]
    public function findById(int $id): ?MaterialPricingByPiece
    {
        return $this->repo->findOneBy(['id' => $id]);
    }
}
