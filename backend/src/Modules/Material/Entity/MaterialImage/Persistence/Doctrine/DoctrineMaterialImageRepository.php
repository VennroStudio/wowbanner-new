<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialImage\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Material\Entity\MaterialImage\MaterialImage;
use App\Modules\Material\Entity\MaterialImage\MaterialImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final class DoctrineMaterialImageRepository implements MaterialImageRepository
{
    /** @var EntityRepository<MaterialImage> */
    private EntityRepository $repo;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(MaterialImage::class);
    }

    #[Override]
    public function add(MaterialImage $image): void
    {
        $this->em->persist($image);
    }

    #[Override]
    public function remove(MaterialImage $image): void
    {
        $this->em->remove($image);
    }

    #[Override]
    public function getById(int $id): MaterialImage
    {
        if (!$image = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: 'material',
                message: 'error.material_image_not_found',
                code: 1
            );
        }

        return $image;
    }

    #[Override]
    public function findById(int $id): ?MaterialImage
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    #[Override]
    public function findByMaterialId(int $materialId): array
    {
        return $this->repo->findBy(['materialId' => $materialId]);
    }
}
