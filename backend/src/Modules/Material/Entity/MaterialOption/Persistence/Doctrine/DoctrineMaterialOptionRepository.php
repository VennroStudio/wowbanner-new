<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialOption\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Material\Entity\MaterialOption\MaterialOption;
use App\Modules\Material\Entity\MaterialOption\MaterialOptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final class DoctrineMaterialOptionRepository implements MaterialOptionRepository
{
    /** @var EntityRepository<MaterialOption> */
    private EntityRepository $repo;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(MaterialOption::class);
    }

    #[Override]
    public function add(MaterialOption $materialOption): void
    {
        $this->em->persist($materialOption);
    }

    #[Override]
    public function remove(MaterialOption $materialOption): void
    {
        $this->em->remove($materialOption);
    }

    #[Override]
    public function getById(int $id): MaterialOption
    {
        if (!$materialOption = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: 'material',
                message: 'error.material_option_not_found',
                code: 1
            );
        }

        return $materialOption;
    }

    #[Override]
    public function findById(int $id): ?MaterialOption
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    #[Override]
    public function findByMaterialId(int $materialId): array
    {
        return $this->repo->findBy(['materialId' => $materialId], ['id' => 'ASC']);
    }
}
