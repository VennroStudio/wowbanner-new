<?php

declare(strict_types=1);

namespace App\Http\Unifier\Material;

use App\Components\Http\Unifier\UnifierHelper;
use App\Components\Http\Unifier\UnifierInterface;
use App\Components\Storage\S3Transformer;
use App\Modules\Material\Query\MaterialImage\FindByMaterialIds\MaterialImageFindByMaterialIdsFetcher;
use App\Modules\Material\Query\MaterialImage\FindByMaterialIds\MaterialImageFindByMaterialIdsQuery;
use App\Modules\Material\ReadModel\Material\Interface\MaterialModelInterface;
use App\Modules\Material\ReadModel\MaterialImage\MaterialImageByMaterial;
use Doctrine\DBAL\Exception;
use Override;

final readonly class MaterialUnifier implements UnifierInterface
{
    public function __construct(
        private S3Transformer $s3Transformer,
        private MaterialImageFindByMaterialIdsFetcher $materialImageFetcher,
    ) {}

    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if (!$item instanceof MaterialModelInterface) {
            return [];
        }

        return $this->unify($userId, [$item])[0] ?? [];
    }

    /**
     * @param list<object> $items
     * @return list<array<string, mixed>>
     * @throws Exception
     */
    #[Override]
    public function unify(?int $userId, array $items): array
    {
        if ($items === []) {
            return [];
        }

        $ids = array_map(static fn(MaterialModelInterface $i): int => $i->getId(), $items);

        $groupedImages = $this->groupImagesByMaterialId(
            $this->materialImageFetcher->fetch(new MaterialImageFindByMaterialIdsQuery($ids))
        );

        return array_map(fn(MaterialModelInterface $item): array => $this->map($item, $groupedImages), $items);
    }

    /**
     * @param array<int, list<array<string, mixed>>> $groupedImages
     * @return array<string, mixed>
     */
    #[Override]
    public function map(object $item, array $groupedImages = []): array
    {
        /** @var MaterialModelInterface $item */
        $data = $item->toArray();
        $data['images'] = $groupedImages[$item->getId()] ?? [];

        return $data;
    }

    /**
     * @param list<MaterialImageByMaterial> $images
     * @return array<int, list<array<string, mixed>>>
     */
    private function groupImagesByMaterialId(array $images): array
    {
        $grouped = [];

        foreach ($images as $image) {
            $data = UnifierHelper::toArrayWithout($image, 'material_id');
            $data = UnifierHelper::transformField($data, 'path', $this->s3Transformer->buildUrl(...));
            $grouped[$image->getMaterialId()][] = $data;
        }

        return $grouped;
    }
}
