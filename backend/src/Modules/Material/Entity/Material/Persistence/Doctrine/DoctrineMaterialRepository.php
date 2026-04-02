<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\Material\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Material\Entity\Material\Material;
use App\Modules\Material\Entity\Material\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineMaterialRepository implements MaterialRepository
{
    /** @var EntityRepository<Material> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(Material::class);
    }

    #[Override]
    public function add(Material $material): void
    {
        $this->em->persist($material);
    }

    #[Override]
    public function remove(Material $material): void
    {
        $this->em->remove($material);
    }

    #[Override]
    public function getById(int $id): Material
    {
        if (!$material = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: 'material',
                message: 'error.material_not_found',
                code: 1
            );
        }

        return $material;
    }

    #[Override]
    public function findById(int $id): ?Material
    {
        return $this->repo->findOneBy(['id' => $id]);
    }
}
