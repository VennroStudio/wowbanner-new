<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialProcessing\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Material\Entity\MaterialProcessing\MaterialProcessing;
use App\Modules\Material\Entity\MaterialProcessing\MaterialProcessingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final class DoctrineMaterialProcessingRepository implements MaterialProcessingRepository
{
    /** @var EntityRepository<MaterialProcessing> */
    private EntityRepository $repo;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(MaterialProcessing::class);
    }

    #[Override]
    public function add(MaterialProcessing $materialProcessing): void
    {
        $this->em->persist($materialProcessing);
    }

    #[Override]
    public function remove(MaterialProcessing $materialProcessing): void
    {
        $this->em->remove($materialProcessing);
    }

    #[Override]
    public function getById(int $id): MaterialProcessing
    {
        if (!$materialProcessing = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: 'material',
                message: 'error.material_processing_not_found',
                code: 1
            );
        }

        return $materialProcessing;
    }

    #[Override]
    public function findById(int $id): ?MaterialProcessing
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    #[Override]
    public function findByMaterialId(int $materialId): array
    {
        return $this->repo->findBy(['materialId' => $materialId], ['id' => 'ASC']);
    }
}
