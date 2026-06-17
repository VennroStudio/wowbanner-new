<?php

declare(strict_types=1);

namespace App\Http\Unifier\Processing;

use App\Components\Http\Unifier\UnifierHelper;
use App\Components\Http\Unifier\UnifierInterface;
use App\Components\Storage\S3Transformer;
use App\Modules\Processing\ReadModel\ProcessingImage\Interface\ProcessingImageModelInterface;
use Override;

final readonly class ProcessingImageUnifier implements UnifierInterface
{
    public function __construct(
        private S3Transformer $s3Transformer,
    ) {}

    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if (!$item instanceof ProcessingImageModelInterface) {
            return [];
        }

        return $this->unify($userId, [$item])[0] ?? [];
    }

    /**
     * @param list<ProcessingImageModelInterface> $items
     * @return list<array<string, mixed>>
     */
    #[Override]
    public function unify(?int $userId, array $items): array
    {
        if ($items === []) {
            return [];
        }

        return array_map($this->map(...), $items);
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function map(object $item): array
    {
        /** @var ProcessingImageModelInterface $item */
        return UnifierHelper::transformField(
            $item->toArray(),
            'path',
            $this->s3Transformer->buildUrl(...),
        );
    }
}
