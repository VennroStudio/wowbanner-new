<?php

declare(strict_types=1);

namespace App\Http\Unifier\Processing;

use App\Components\Http\Unifier\UnifierInterface;
use App\Components\Storage\S3Transformer;
use App\Modules\Processing\Query\ProcessingImage\FindByProcessingIds\ProcessingImageFindByProcessingIdsFetcher;
use App\Modules\Processing\Query\ProcessingImage\FindByProcessingIds\ProcessingImageFindByProcessingIdsQuery;
use App\Modules\Processing\ReadModel\Processing\Interface\ProcessingModelInterface;
use App\Modules\Processing\ReadModel\ProcessingImage\ProcessingImageByProcessing;
use Override;

final readonly class ProcessingUnifier implements UnifierInterface
{
    public function __construct(
        private S3Transformer $s3Transformer,
        private ProcessingImageFindByProcessingIdsFetcher $imageFetcher,
    ) {}

    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if (!$item instanceof ProcessingModelInterface) {
            return [];
        }

        return $this->unify($userId, [$item])[0] ?? [];
    }

    /**
     * @param list<object> $items
     * @return list<array<string, mixed>>
     */
    #[Override]
    public function unify(?int $userId, array $items): array
    {
        if ($items === []) {
            return [];
        }

        $ids = array_map(static fn(ProcessingModelInterface $i): int => $i->getId(), $items);
        $groupedImages = $this->groupImagesByProcessingId(
            $this->imageFetcher->fetch(new ProcessingImageFindByProcessingIdsQuery($ids))
        );

        return array_map(fn(ProcessingModelInterface $item): array => $this->map($item, $groupedImages), $items);
    }

    /**
     * @param array<int, list<array<string, mixed>>> $groupedImages
     * @return array<string, mixed>
     */
    #[Override]
    public function map(object $item, array $groupedImages = []): array
    {
        /** @var ProcessingModelInterface $item */
        $data = $item->toArray();
        $data['images'] = $groupedImages[$item->getId()] ?? [];

        return $data;
    }

    /**
     * @param list<ProcessingImageByProcessing> $images
     * @return array<int, list<array<string, mixed>>>
     */
    private function groupImagesByProcessingId(array $images): array
    {
        $grouped = [];

        foreach ($images as $image) {
            $data = $image->toArray();
            $data['path'] = $this->s3Transformer->buildUrl($data['path']);
            unset($data['processing_id']);
            $grouped[$image->getProcessingId()][] = $data;
        }

        return $grouped;
    }
}
