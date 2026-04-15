<?php

declare(strict_types=1);

namespace App\Modules\Production\Entity\Production\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Production\Entity\Production\Production;
use App\Modules\Production\Entity\Production\ProductionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineProductionRepository implements ProductionRepository
{
    /** @var EntityRepository<Production> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(Production::class);
    }

    #[Override]
    public function getById(int $id): Production
    {
        $production = $this->findById($id);
        if ($production === null) {
            throw new DomainExceptionModule(
                module: 'production',
                message: 'error.production_not_found',
                code: 1
            );
        }

        return $production;
    }

    #[Override]
    public function findById(int $id): ?Production
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    #[Override]
    public function add(Production $production): void
    {
        $this->em->persist($production);
    }

    #[Override]
    public function remove(Production $production): void
    {
        $this->em->remove($production);
    }
}
