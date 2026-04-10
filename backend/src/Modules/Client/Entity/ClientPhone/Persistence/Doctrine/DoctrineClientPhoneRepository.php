<?php

declare(strict_types=1);

namespace App\Modules\Client\Entity\ClientPhone\Persistence\Doctrine;

use App\Modules\Client\Entity\ClientPhone\ClientPhone;
use App\Modules\Client\Entity\ClientPhone\ClientPhoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final readonly class DoctrineClientPhoneRepository implements ClientPhoneRepository
{
    /** @var EntityRepository<ClientPhone> */
    private EntityRepository $repository;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repository = $em->getRepository(ClientPhone::class);
    }

    public function add(ClientPhone $phone): void
    {
        $this->em->persist($phone);
    }

    public function remove(ClientPhone $phone): void
    {
        $this->em->remove($phone);
    }
}
