<?php

declare(strict_types=1);

namespace App\Modules\Material\Service;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Material\Entity\MaterialOption\Fields\Enums\MaterialOptionPricingType;
use App\Modules\Material\ReadModel\MaterialOption\MaterialOptionItem;

final readonly class MaterialValidatorService
{
    /**
     * @param list<MaterialOptionItem> $options
     */
    public function validateOptions(array $options): void
    {
        foreach ($options as $option) {
            $this->validatePricingShape($option);
            $this->validateDuplicates($option);
        }
    }

    private function validatePricingShape(MaterialOptionItem $option): void
    {
        if (
            $option->pricingType === MaterialOptionPricingType::BY_AREA->value
            && $option->pricingByPiece !== []
        ) {
            throw new DomainExceptionModule(
                module: 'material',
                message: 'error.material_option_by_area_cannot_have_piece_pricing',
                code: 2,
            );
        }

        if (
            $option->pricingType === MaterialOptionPricingType::BY_PIECE->value
            && $option->pricingByArea !== []
        ) {
            throw new DomainExceptionModule(
                module: 'material',
                message: 'error.material_option_by_piece_cannot_have_area_pricing',
                code: 3,
            );
        }

        if (!$option->isCut && $option->pricingByCut !== []) {
            throw new DomainExceptionModule(
                module: 'material',
                message: 'error.material_option_without_cut_cannot_have_cut_pricing',
                code: 4,
            );
        }
    }

    private function validateDuplicates(MaterialOptionItem $option): void
    {
        $this->assertUnique(
            rows: $option->pricingByArea,
            keyBuilder: static fn (object $row): string => $row->dpiType . ':' . $row->areaRangeType,
            message: 'error.material_pricing_by_area_duplicate',
            code: 5,
        );
        $this->assertUnique(
            rows: $option->pricingByPiece,
            keyBuilder: static fn (object $row): string => (string) $row->variantType,
            message: 'error.material_pricing_by_piece_duplicate',
            code: 6,
        );
        $this->assertUnique(
            rows: $option->pricingByCut,
            keyBuilder: static fn (object $row): string => (string) $row->type,
            message: 'error.material_pricing_cut_duplicate',
            code: 7,
        );
        $this->assertUnique(
            rows: $option->processings,
            keyBuilder: static fn (object $row): string => (string) $row->processingId,
            message: 'error.material_processing_duplicate',
            code: 8,
        );
    }

    /**
     * @param list<object> $rows
     * @param callable(object): string $keyBuilder
     */
    private function assertUnique(array $rows, callable $keyBuilder, string $message, int $code): void
    {
        $seen = [];

        foreach ($rows as $row) {
            $key = $keyBuilder($row);
            if (isset($seen[$key])) {
                throw new DomainExceptionModule(
                    module: 'material',
                    message: $message,
                    code: $code,
                );
            }

            $seen[$key] = true;
        }
    }
}
