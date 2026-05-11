<?php

declare(strict_types=1);

namespace App\Http\Unifier\Product;

use App\Components\Http\Unifier\UnifierHelper;
use App\Components\Http\Unifier\UnifierInterface;
use App\Modules\Material\Query\Material\GetById\MaterialGetByIdFetcher;
use App\Modules\Material\Query\Material\GetById\MaterialGetByIdQuery;
use App\Modules\Material\Query\MaterialOption\GetById\MaterialOptionGetByIdFetcher;
use App\Modules\Material\Query\MaterialOption\GetById\MaterialOptionGetByIdQuery;
use App\Modules\Product\Query\ProductMaterial\FindByProductIds\ProductMaterialFindByProductIdsFetcher;
use App\Modules\Product\Query\ProductMaterial\FindByProductIds\ProductMaterialFindByProductIdsQuery;
use App\Modules\Product\Query\ProductPrint\FindByProductIds\ProductPrintFindByProductIdsFetcher;
use App\Modules\Product\Query\ProductPrint\FindByProductIds\ProductPrintFindByProductIdsQuery;
use App\Modules\Product\ReadModel\Product\Interface\ProductModelInterface;
use App\Modules\Product\ReadModel\ProductMaterial\ProductMaterialByProductId;
use App\Modules\Product\ReadModel\ProductPrint\ProductPrintByProductId;
use App\Modules\Printing\Query\Printing\GetById\PrintingGetByIdFetcher;
use App\Modules\Printing\Query\Printing\GetById\PrintingGetByIdQuery;
use Doctrine\DBAL\Exception;
use Override;

final readonly class ProductUnifier implements UnifierInterface
{
    public function __construct(
        private ProductMaterialFindByProductIdsFetcher $materialFetcher,
        private ProductPrintFindByProductIdsFetcher    $printFetcher,
        private MaterialGetByIdFetcher                $materialByIdFetcher,
        private MaterialOptionGetByIdFetcher          $materialOptionFetcher,
        private PrintingGetByIdFetcher                $printingByIdFetcher,
    ) {}

    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if ($item === null) {
            return [];
        }

        return $this->unify($userId, [$item])[0] ?? [];
    }

    /**
     * @param list<ProductModelInterface> $items
     * @return list<array<string, mixed>>
     * @throws Exception
     */
    #[Override]
    public function unify(?int $userId, array $items): array
    {
        if ($items === []) {
            return [];
        }

        $ids = array_map(static fn(ProductModelInterface $i): int => $i->getId(), $items);

        $materialsByProduct = $this->groupMaterials(
            $this->materialFetcher->fetch(new ProductMaterialFindByProductIdsQuery($ids)),
        );
        $printsByProduct = $this->groupPrints(
            $this->printFetcher->fetch(new ProductPrintFindByProductIdsQuery($ids)),
        );

        return array_map(
            fn(ProductModelInterface $item): array => $this->map($item, $materialsByProduct, $printsByProduct),
            $items,
        );
    }

    /**
     * @param array<int, list<array<string, mixed>>> $materialsByProduct
     * @param array<int, list<array<string, mixed>>> $printsByProduct
     */
    #[Override]
    public function map(object $item, array $materialsByProduct = [], array $printsByProduct = []): array
    {
        /** @var ProductModelInterface $item */
        $data = $item->toArray();
        $data['materials'] = $materialsByProduct[$item->getId()] ?? [];
        $data['prints'] = $printsByProduct[$item->getId()] ?? [];

        return UnifierHelper::withTimestamps($data, $item);
    }

    /**
     * @param list<ProductMaterialByProductId> $items
     * @return array<int, list<array<string, mixed>>>
     */
    private function groupMaterials(array $items): array
    {
        $grouped = [];
        foreach ($items as $item) {
            $option = $this->materialOptionFetcher->fetch(new MaterialOptionGetByIdQuery($item->materialOptionId));
            $material = $this->materialByIdFetcher->fetch(new MaterialGetByIdQuery($item->materialId));
            $grouped[$item->productId][] = [
                'id' => $item->id,
                'material_id' => $item->materialId,
                'material_option_id' => $item->materialOptionId,
                'material_name' => $material->name,
                'material_option_name' => $option->name,
            ];
        }

        return $grouped;
    }

    /**
     * @param list<ProductPrintByProductId> $items
     * @return array<int, list<array<string, mixed>>>
     */
    private function groupPrints(array $items): array
    {
        $grouped = [];
        foreach ($items as $item) {
            $print = $this->printingByIdFetcher->fetch(new PrintingGetByIdQuery($item->printId));
            $grouped[$item->productId][] = [
                'id' => $item->id,
                'print_id' => $item->printId,
                'print_name' => $print->name,
            ];
        }

        return $grouped;
    }
}
