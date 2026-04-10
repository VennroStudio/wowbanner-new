<?php

declare(strict_types=1);

namespace App\Modules\Client\Entity\Client\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Client\Entity\Client\Client;
use App\Modules\Client\Entity\Client\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineClientRepository implements ClientRepository
{
    /** @var EntityRepository<Client> */
    private EntityRepository $repo;

    public function __construct(private EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Client::class);
    }

    #[Override]
    public function getById(int $id): Client
    {
        $client = $this->findById($id);
        if ($client === null) {
            throw new DomainExceptionModule(
                module: 'client',
                message: 'error.client_not_found',
                code: 1
            );
        }
        return $client;
    }

    #[Override]
    public function findById(int $id): ?Client
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    #[Override]
    public function findByEmail(string $email): ?Client
    {
        return $this->repo->findOneBy(['email' => $email]);
    }

    #[Override]
    public function findByPhone(string $phone): ?Client
    {
        return $this->repo->findOneBy(['phone' => $phone]);
    }

    #[Override]
    public function add(Client $client): void
    {
        $this->em->persist($client);
    }

    #[Override]
    public function remove(Client $client): void
    {
        $this->em->remove($client);
    }
}
