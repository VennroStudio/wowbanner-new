<?php

declare(strict_types=1);

namespace App\Http\Unifier\Processing;

use App\Components\Http\Unifier\UnifierInterface;
use App\Modules\Processing\Query\ProcessingImage\FindByProcessingIds\ProcessingImageFindByProcessingIdsFetcher;
use App\Modules\Processing\Query\ProcessingImage\FindByProcessingIds\ProcessingImageFindByProcessingIdsQuery;
use App\Modules\Processing\ReadModel\Processing\Interface\ProcessingModelInterface;
use App\Modules\Processing\ReadModel\ProcessingImage\Interface\ProcessingImageModelInterface;
use Doctrine\DBAL\Exception;
use Override;

final readonly class ProcessingUnifier implements UnifierInterface
{
    public function __construct(
        private ProcessingImageFindByProcessingIdsFetcher $imageFetcher,
        private ProcessingImageUnifier $imageUnifier,
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
     * @param list<ProcessingModelInterface> $items
     * @return list<array<string, mixed>>
     * @throws Exception
     */
    #[Override]
    public function unify(?int $userId, array $items): array
    {
        if ($items === []) {
            return [];
        }

        $ids = array_map(static fn (ProcessingModelInterface $i): int => $i->getId(), $items);

        $groupedImages = $this->groupImagesByProcessingId(
            $this->imageFetcher->fetch(new ProcessingImageFindByProcessingIdsQuery($ids))
        );

        return array_map(fn (ProcessingModelInterface $item): array => $this->map($item, $groupedImages), $items);
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
     * @param list<ProcessingImageModelInterface> $images
     * @return array<int, list<array<string, mixed>>>
     */
    private function groupImagesByProcessingId(array $images): array
    {
        $grouped = [];

        foreach ($images as $image) {
            $grouped[$image->getProcessingId()][] = $image;
        }

        $result = [];
        foreach ($grouped as $processingId => $group) {
            $result[$processingId] = $this->imageUnifier->unify(null, $group);
        }

        return $result;
    }
}
