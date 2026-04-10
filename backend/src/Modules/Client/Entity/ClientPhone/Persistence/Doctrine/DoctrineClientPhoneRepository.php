<?php

declare(strict_types=1);

namespace App\Modules\Client\Entity\ClientPhone\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Client\Entity\ClientPhone\ClientPhone;
use App\Modules\Client\Entity\ClientPhone\ClientPhoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineClientPhoneRepository implements ClientPhoneRepository
{
    /** @var EntityRepository<ClientPhone> */
    private EntityRepository $repository;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repository = $em->getRepository(ClientPhone::class);
    }

    #[Override]
    public function getById(int $id): ClientPhone
    {
        $phone = $this->findById($id);
        if ($phone === null) {
            throw new DomainExceptionModule(
                module: 'client',
                message: 'error.client_phone_not_found',
                code: 3
            );
        }
        return $phone;
    }

    #[Override]
    public function findById(int $id): ?ClientPhone
    {
        return $this->repository->findOneBy(['id' => $id]);
    }

    #[Override]
    public function findByClientId(int $clientId): array
    {
        return $this->repository->findBy(['clientId' => $clientId]);
    }

    #[Override]
    public function add(ClientPhone $phone): void
    {
        $this->em->persist($phone);
    }

    #[Override]
    public function remove(ClientPhone $phone): void
    {
        $this->em->remove($phone);
    }
}
