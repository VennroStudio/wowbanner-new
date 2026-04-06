<?php

declare(strict_types=1);

namespace App\Modules\Gallery\Entity\Gallery\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Gallery\Entity\Gallery\Gallery;
use App\Modules\Gallery\Entity\Gallery\GalleryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineGalleryRepository implements GalleryRepository
{
    /** @var EntityRepository<Gallery> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(Gallery::class);
    }

    #[Override]
    public function add(Gallery $gallery): void
    {
        $this->em->persist($gallery);
    }

    #[Override]
    public function remove(Gallery $gallery): void
    {
        $this->em->remove($gallery);
    }

    #[Override]
    public function getById(int $id): Gallery
    {
        if (!$gallery = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: 'gallery',
                message: 'error.gallery_not_found',
                code: 1
            );
        }

        return $gallery;
    }

    #[Override]
    public function findById(int $id): ?Gallery
    {
        return $this->repo->findOneBy(['id' => $id]);
    }
}
