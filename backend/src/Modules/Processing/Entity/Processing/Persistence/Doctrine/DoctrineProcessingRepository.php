<?php

declare(strict_types=1);

namespace App\Modules\Processing\Entity\Processing\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Processing\Entity\Processing\Processing;
use App\Modules\Processing\Entity\Processing\ProcessingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final class DoctrineProcessingRepository implements ProcessingRepository
{
    /** @var EntityRepository<Processing> */
    private EntityRepository $repo;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        /** @var EntityRepository<Processing> $repo */
        $repo = $em->getRepository(Processing::class);
        $this->repo = $repo;
    }

    #[Override]
    public function add(Processing $processing): void
    {
        $this->em->persist($processing);
    }

    #[Override]
    public function getById(int $id): Processing
    {
        if (!($processing = $this->findById($id))) {
            throw new DomainExceptionModule(
                module: 'processing',
                message: 'error.processing_not_found',
                code: 1
            );
        }

        return $processing;
    }

    #[Override]
    public function findById(int $id): ?Processing
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    #[Override]
    public function remove(Processing $processing): void
    {
        $this->em->remove($processing);
    }
}
