<?php

declare(strict_types=1);

namespace App\Modules\Processing\Entity\ProcessingImage\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Processing\Entity\ProcessingImage\ProcessingImage;
use App\Modules\Processing\Entity\ProcessingImage\ProcessingImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final class DoctrineProcessingImageRepository implements ProcessingImageRepository
{
    /** @var EntityRepository<ProcessingImage> */
    private EntityRepository $repo;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        /** @var EntityRepository<ProcessingImage> $repo */
        $repo = $em->getRepository(ProcessingImage::class);
        $this->repo = $repo;
    }

    #[Override]
    public function add(ProcessingImage $image): void
    {
        $this->em->persist($image);
    }

    #[Override]
    public function remove(ProcessingImage $image): void
    {
        $this->em->remove($image);
    }

    #[Override]
    public function getById(int $id): ProcessingImage
    {
        if (!($image = $this->findById($id))) {
            throw new DomainExceptionModule(
                module: 'processing',
                message: 'error.processing_image_not_found',
                code: 1
            );
        }

        return $image;
    }

    #[Override]
    public function findById(int $id): ?ProcessingImage
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    #[Override]
    public function findByProcessingId(int $processingId): array
    {
        return $this->repo->findBy(['processingId' => $processingId]);
    }
}
