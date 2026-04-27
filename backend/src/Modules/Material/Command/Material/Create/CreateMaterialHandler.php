<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\Material\Create;

use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Material\Command\MaterialOption\Create\CreateMaterialOptionCommand;
use App\Modules\Material\Command\MaterialOption\Create\CreateMaterialOptionHandler;
use App\Modules\Material\Command\MaterialPricingByArea\Create\CreateMaterialPricingByAreaCommand;
use App\Modules\Material\Command\MaterialPricingByArea\Create\CreateMaterialPricingByAreaHandler;
use App\Modules\Material\Command\MaterialPricingByPiece\Create\CreateMaterialPricingByPieceCommand;
use App\Modules\Material\Command\MaterialPricingByPiece\Create\CreateMaterialPricingByPieceHandler;
use App\Modules\Material\Command\MaterialPricingCut\Create\CreateMaterialPricingCutCommand;
use App\Modules\Material\Command\MaterialPricingCut\Create\CreateMaterialPricingCutHandler;
use App\Modules\Material\Command\MaterialProcessing\Create\CreateMaterialProcessingCommand;
use App\Modules\Material\Command\MaterialProcessing\Create\CreateMaterialProcessingHandler;
use App\Modules\Material\Entity\Material\Material;
use App\Modules\Material\Entity\Material\MaterialRepository;
use App\Modules\Material\Entity\MaterialOption\MaterialOptionRepository;
use App\Modules\Material\Permission\MaterialPermission;
use App\Modules\Material\ReadModel\MaterialOption\MaterialOptionItem;
use App\Modules\Material\ReadModel\MaterialPricingByArea\MaterialPricingByAreaItem;
use App\Modules\Material\ReadModel\MaterialPricingByPiece\MaterialPricingByPieceItem;
use App\Modules\Material\ReadModel\MaterialPricingCut\MaterialPricingCutItem;
use App\Modules\Material\ReadModel\MaterialProcessing\MaterialProcessingLinkItem;
use App\Modules\Material\Service\MaterialPermissionService;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;
use Doctrine\DBAL\Exception;

final readonly class CreateMaterialHandler
{
    public function __construct(
        private MaterialRepository $materialRepository,
        private MaterialOptionRepository $materialOptionRepository,
        private MaterialPermissionService $materialPermissionService,
        private FlusherInterface $flusher,
        private CreateMaterialOptionHandler $createMaterialOptionHandler,
        private CreateMaterialPricingByAreaHandler $createMaterialPricingByAreaHandler,
        private CreateMaterialPricingByPieceHandler $createMaterialPricingByPieceHandler,
        private CreateMaterialPricingCutHandler $createMaterialPricingCutHandler,
        private CreateMaterialProcessingHandler $createMaterialProcessingHandler,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
    ) {}

    /** @throws AccessDeniedException|Exception */
    public function handle(CreateMaterialCommand $command): void
    {
        $this->materialPermissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: MaterialPermission::CREATE,
        );

        $material = Material::create(
            name: $command->name,
            description: $command->description,
        );

        $this->materialRepository->add($material);
        $this->flusher->flush();

        $materialId = (int) $material->id;
        $this->processOptions($materialId, $command->options);

        $this->flusher->flush();

        $this->materialQueryCacheInvalidator->invalidateByMaterialId($materialId);
        foreach ($this->materialOptionRepository->findByMaterialId($materialId) as $option) {
            $this->materialQueryCacheInvalidator->invalidateMaterialOption(
                (int) $option->id,
                $materialId
            );
        }
    }

    /**
     * @param list<MaterialOptionItem> $options
     * @throws Exception
     */
    private function processOptions(int $materialId, array $options): void
    {
        foreach ($options as $item) {
            $option = $this->createMaterialOptionHandler->handle(
                new CreateMaterialOptionCommand(
                    name: $item->name,
                    materialId: $materialId,
                    pricingType: $item->pricingType,
                    isCut: $item->isCut,
                )
            );
            $this->flusher->flush();
            $optionId = (int) $option->id;

            $this->createAreaRows($materialId, $optionId, $item->pricingByArea);
            $this->createPieceRows($materialId, $optionId, $item->pricingByPiece);
            $this->createCutRows($materialId, $optionId, $item->pricingByCut);
            $this->createProcessingRows($materialId, $optionId, $item->processings);
        }
    }

    /**
     * @param list<MaterialPricingByAreaItem> $rows
     */
    private function createAreaRows(int $materialId, int $optionId, array $rows): void
    {
        foreach ($rows as $row) {
            $this->createMaterialPricingByAreaHandler->handle(
                new CreateMaterialPricingByAreaCommand(
                    materialId: $materialId,
                    optionId: $optionId,
                    dpiType: $row->dpiType,
                    areaRangeType: $row->areaRangeType,
                    price: $row->price,
                    cost: $row->cost,
                    printHours: $row->printHours,
                )
            );
        }
    }

    /**
     * @param list<MaterialPricingByPieceItem> $rows
     */
    private function createPieceRows(int $materialId, int $optionId, array $rows): void
    {
        foreach ($rows as $row) {
            $this->createMaterialPricingByPieceHandler->handle(
                new CreateMaterialPricingByPieceCommand(
                    materialId: $materialId,
                    optionId: $optionId,
                    variantType: $row->variantType,
                    price: $row->price,
                    cost: $row->cost,
                    printHours: $row->printHours,
                )
            );
        }
    }

    /**
     * @param list<MaterialPricingCutItem> $rows
     */
    private function createCutRows(int $materialId, int $optionId, array $rows): void
    {
        foreach ($rows as $row) {
            $this->createMaterialPricingCutHandler->handle(
                new CreateMaterialPricingCutCommand(
                    materialId: $materialId,
                    optionId: $optionId,
                    type: $row->type,
                    price: $row->price,
                )
            );
        }
    }

    /**
     * @param list<MaterialProcessingLinkItem> $rows
     * @throws Exception
     */
    private function createProcessingRows(int $materialId, int $optionId, array $rows): void
    {
        foreach ($rows as $row) {
            $this->createMaterialProcessingHandler->handle(
                new CreateMaterialProcessingCommand(
                    materialId: $materialId,
                    optionId: $optionId,
                    processingId: $row->processingId,
                )
            );
        }
    }
}
