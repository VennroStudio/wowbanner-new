<?php

declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Components\YandexDisk\YandexDiskClient;
use App\Modules\Order\Entity\OrderFile\Fields\Enums\OrderFileDirectory;
use Random\RandomException;

final readonly class OrderFileStorageService
{
    public function __construct(
        private YandexDiskClient $yandexDiskClient,
    ) {}

    /**
     * @return array{diskPath: string, fileName: string, originalName: string}
     * @throws RandomException
     */
    public function upload(int $orderId, string $tmpFilePath, string $originalName): array
    {
        $fileName = $this->buildFileName($originalName);
        $diskPath = $this->yandexDiskClient->upload(
            tmpFilePath: $tmpFilePath,
            folder: OrderFileDirectory::FILES->getPath($orderId),
            fileName: $fileName,
        );

        return [
            'diskPath' => $diskPath,
            'fileName' => $fileName,
            'originalName' => $originalName,
        ];
    }

    /**
     * @return array{diskPath: string, fileName: string, originalName: string}
     * @throws RandomException
     */
    public function replace(int $orderId, string $oldDiskPath, string $tmpFilePath, string $originalName): array
    {
        $this->delete($oldDiskPath);

        return $this->upload(
            orderId: $orderId,
            tmpFilePath: $tmpFilePath,
            originalName: $originalName,
        );
    }

    public function delete(string $diskPath): void
    {
        $this->yandexDiskClient->delete($diskPath);
    }

    public function download(string $diskPath): string
    {
        return $this->yandexDiskClient->download($diskPath);
    }

    /**
     * @throws RandomException
     */
    private function buildFileName(string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $extension = $extension !== '' ? '.' . mb_strtolower($extension) : '';

        return bin2hex(random_bytes(16)) . $extension;
    }
}
