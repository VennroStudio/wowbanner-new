<?php

declare(strict_types=1);

use App\Modules\Gallery\Entity\Gallery\GalleryRepository;
use App\Modules\Gallery\Entity\Gallery\Persistence\Doctrine\DoctrineGalleryRepository;
use App\Modules\Material\Entity\Material\MaterialRepository;
use App\Modules\Material\Entity\Material\Persistence\Doctrine\DoctrineMaterialRepository;
use App\Modules\Material\Entity\MaterialImage\MaterialImageRepository;
use App\Modules\Material\Entity\MaterialImage\Persistence\Doctrine\DoctrineMaterialImageRepository;
use App\Modules\Printing\Entity\Printing\Persistence\Doctrine\DoctrinePrintingRepository;
use App\Modules\Printing\Entity\Printing\PrintingRepository;
use App\Modules\User\Entity\User\Persistence\Doctrine\DoctrineUserRepository;
use App\Modules\User\Entity\User\UserRepository;
use App\Modules\User\Entity\UserToken\Persistence\Doctrine\DoctrineUserTokenRepository;
use App\Modules\User\Entity\UserToken\UserTokenRepository;

use function DI\get;

return [
    GalleryRepository::class   => get(DoctrineGalleryRepository::class),
    MaterialRepository::class      => get(DoctrineMaterialRepository::class),
    MaterialImageRepository::class => get(DoctrineMaterialImageRepository::class),
    PrintingRepository::class      => get(DoctrinePrintingRepository::class),
    UserRepository::class      => get(DoctrineUserRepository::class),
    UserTokenRepository::class => get(DoctrineUserTokenRepository::class),
];
