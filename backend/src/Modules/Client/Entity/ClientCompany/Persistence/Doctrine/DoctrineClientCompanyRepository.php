<?php

declare(strict_types=1);

namespace App\Modules\Client\Entity\ClientCompany\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Client\Entity\ClientCompany\ClientCompany;
use App\Modules\Client\Entity\ClientCompany\ClientCompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineClientCompanyRepository implements ClientCompanyRepository
{
    /** @var EntityRepository<ClientCompany> */
    private EntityRepository $repo;

    public function __construct(private EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(ClientCompany::class);
    }

    #[Override]
    public function getById(int $id): ClientCompany
    {
        $company = $this->findById($id);
        if ($company === null) {
            throw new DomainExceptionModule(
                module: 'client',
                message: 'error.client_company_not_found',
                code: 2
            );
        }
        return $company;
    }

    #[Override]
    public function findById(int $id): ?ClientCompany
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    #[Override]
    public function findByClientId(int $clientId): array
    {
        return $this->repo->findBy(['clientId' => $clientId]);
    }

    #[Override]
    public function add(ClientCompany $company): void
    {
        $this->em->persist($company);
    }

    #[Override]
    public function remove(ClientCompany $company): void
    {
        $this->em->remove($company);
    }
}
