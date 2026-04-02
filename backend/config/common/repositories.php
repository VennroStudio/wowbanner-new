<?php

declare(strict_types=1);

use App\Modules\Material\Entity\Material\MaterialRepository;
use App\Modules\Material\Entity\Material\Persistence\Doctrine\DoctrineMaterialRepository;
use App\Modules\Printing\Entity\Printing\Persistence\Doctrine\DoctrinePrintingRepository;
use App\Modules\Printing\Entity\Printing\PrintingRepository;
use App\Modules\User\Entity\User\Persistence\Doctrine\DoctrineUserRepository;
use App\Modules\User\Entity\User\UserRepository;
use App\Modules\User\Entity\UserToken\Persistence\Doctrine\DoctrineUserTokenRepository;
use App\Modules\User\Entity\UserToken\UserTokenRepository;

use function DI\get;

return [
    MaterialRepository::class  => get(DoctrineMaterialRepository::class),
    PrintingRepository::class  => get(DoctrinePrintingRepository::class),
    UserRepository::class      => get(DoctrineUserRepository::class),
    UserTokenRepository::class => get(DoctrineUserTokenRepository::class),
];
