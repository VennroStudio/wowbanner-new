<?php

declare(strict_types=1);

use App\Modules\Material\Entity\Material\MaterialRepository;
use App\Modules\Material\Entity\Material\Persistence\Doctrine\DoctrineMaterialRepository;
use App\Modules\Material\Entity\MaterialImage\MaterialImageRepository;
use App\Modules\Material\Entity\MaterialImage\Persistence\Doctrine\DoctrineMaterialImageRepository;
use App\Modules\Material\Entity\MaterialOption\MaterialOptionRepository;
use App\Modules\Material\Entity\MaterialOption\Persistence\Doctrine\DoctrineMaterialOptionRepository;
use App\Modules\Material\Entity\MaterialPricingByArea\MaterialPricingByAreaRepository;
use App\Modules\Material\Entity\MaterialPricingByArea\Persistence\Doctrine\DoctrineMaterialPricingByAreaRepository;
use App\Modules\Material\Entity\MaterialPricingByPiece\MaterialPricingByPieceRepository;
use App\Modules\Material\Entity\MaterialPricingByPiece\Persistence\Doctrine\DoctrineMaterialPricingByPieceRepository;
use App\Modules\Material\Entity\MaterialPricingCut\MaterialPricingCutRepository;
use App\Modules\Material\Entity\MaterialPricingCut\Persistence\Doctrine\DoctrineMaterialPricingCutRepository;
use App\Modules\Material\Entity\MaterialProcessing\MaterialProcessingRepository;
use App\Modules\Material\Entity\MaterialProcessing\Persistence\Doctrine\DoctrineMaterialProcessingRepository;
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
use App\Modules\Product\Entity\Product\Persistence\Doctrine\DoctrineProductRepository;
use App\Modules\Product\Entity\Product\ProductRepository;
use App\Modules\Product\Entity\ProductMaterial\Persistence\Doctrine\DoctrineProductMaterialRepository;
use App\Modules\Product\Entity\ProductMaterial\ProductMaterialRepository;
use App\Modules\Product\Entity\ProductPrint\Persistence\Doctrine\DoctrineProductPrintRepository;
use App\Modules\Product\Entity\ProductPrint\ProductPrintRepository;

use function DI\get;

return [
    MaterialRepository::class                => get(DoctrineMaterialRepository::class),
    MaterialImageRepository::class          => get(DoctrineMaterialImageRepository::class),
    MaterialOptionRepository::class         => get(DoctrineMaterialOptionRepository::class),
    MaterialPricingByAreaRepository::class  => get(DoctrineMaterialPricingByAreaRepository::class),
    MaterialPricingByPieceRepository::class => get(DoctrineMaterialPricingByPieceRepository::class),
    MaterialProcessingRepository::class     => get(DoctrineMaterialProcessingRepository::class),
    MaterialPricingCutRepository::class     => get(DoctrineMaterialPricingCutRepository::class),
    PrintingRepository::class      => get(DoctrinePrintingRepository::class),
    UserRepository::class      => get(DoctrineUserRepository::class),
    UserTokenRepository::class => get(DoctrineUserTokenRepository::class),
    ProcessingRepository::class      => get(DoctrineProcessingRepository::class),
    ProcessingImageRepository::class => get(DoctrineProcessingImageRepository::class),
    ClientRepository::class   => get(DoctrineClientRepository::class),
    ClientCompanyRepository::class => get(DoctrineClientCompanyRepository::class),
    ClientPhoneRepository::class => get(DoctrineClientPhoneRepository::class),
    ProductRepository::class           => get(DoctrineProductRepository::class),
    ProductMaterialRepository::class   => get(DoctrineProductMaterialRepository::class),
    ProductPrintRepository::class      => get(DoctrineProductPrintRepository::class),
];
