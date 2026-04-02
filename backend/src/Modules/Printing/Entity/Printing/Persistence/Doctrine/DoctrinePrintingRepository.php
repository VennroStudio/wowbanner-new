<?php

declare(strict_types=1);

namespace App\Modules\Printing\Entity\Printing\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Printing\Entity\Printing\Printing;
use App\Modules\Printing\Entity\Printing\PrintingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrinePrintingRepository implements PrintingRepository
{
    /** @var EntityRepository<Printing> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(Printing::class);
    }

    #[Override]
    public function add(Printing $printing): void
    {
        $this->em->persist($printing);
    }

    #[Override]
    public function remove(Printing $printing): void
    {
        $this->em->remove($printing);
    }

    #[Override]
    public function getById(int $id): Printing
    {
        if (!$printing = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: 'printing',
                message: 'error.printing_not_found',
                code: 1
            );
        }

        return $printing;
    }

    #[Override]
    public function findById(int $id): ?Printing
    {
        return $this->repo->findOneBy(['id' => $id]);
    }
}
