<?php

declare(strict_types=1);

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
use App\Modules\Processing\Entity\Processing\Persistence\Doctrine\DoctrineProcessingRepository;
use App\Modules\Processing\Entity\Processing\ProcessingRepository;
use App\Modules\Processing\Entity\ProcessingImage\Persistence\Doctrine\DoctrineProcessingImageRepository;
use App\Modules\Processing\Entity\ProcessingImage\ProcessingImageRepository;
use App\Modules\Client\Entity\Client\Persistence\Doctrine\DoctrineClientRepository;
use App\Modules\Client\Entity\Client\ClientRepository;
use App\Modules\Client\Entity\ClientPhone\Persistence\Doctrine\DoctrineClientPhoneRepository;
use App\Modules\Client\Entity\ClientPhone\ClientPhoneRepository;
use App\Modules\Client\Entity\ClientCompany\Persistence\Doctrine\DoctrineClientCompanyRepository;
use App\Modules\Client\Entity\ClientCompany\ClientCompanyRepository;
use App\Modules\Production\Entity\Production\Persistence\Doctrine\DoctrineProductionRepository;
use App\Modules\Production\Entity\Production\ProductionRepository;
use App\Modules\Production\Entity\ProductionMaterial\Persistence\Doctrine\DoctrineProductionMaterialRepository;
use App\Modules\Production\Entity\ProductionMaterial\ProductionMaterialRepository;
use App\Modules\Production\Entity\ProductionPrint\Persistence\Doctrine\DoctrineProductionPrintRepository;
use App\Modules\Production\Entity\ProductionPrint\ProductionPrintRepository;

use function DI\get;

return [
    MaterialRepository::class      => get(DoctrineMaterialRepository::class),
    MaterialImageRepository::class => get(DoctrineMaterialImageRepository::class),
    PrintingRepository::class      => get(DoctrinePrintingRepository::class),
    UserRepository::class      => get(DoctrineUserRepository::class),
    UserTokenRepository::class => get(DoctrineUserTokenRepository::class),
    ProcessingRepository::class      => get(DoctrineProcessingRepository::class),
    ProcessingImageRepository::class => get(DoctrineProcessingImageRepository::class),
    ClientRepository::class   => get(DoctrineClientRepository::class),
    ClientCompanyRepository::class => get(DoctrineClientCompanyRepository::class),
    ClientPhoneRepository::class => get(DoctrineClientPhoneRepository::class),
    ProductionRepository::class           => get(DoctrineProductionRepository::class),
    ProductionMaterialRepository::class   => get(DoctrineProductionMaterialRepository::class),
    ProductionPrintRepository::class      => get(DoctrineProductionPrintRepository::class),
];
