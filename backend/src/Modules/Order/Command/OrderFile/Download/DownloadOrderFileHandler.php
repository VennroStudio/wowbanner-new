<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderFile\Download;

use App\Components\Exception\DomainExceptionModule;
use App\Components\YandexDisk\YandexDiskClient;
use App\Modules\Order\Entity\OrderFile\OrderFileRepository;
use GuzzleHttp\Client;

final readonly class DownloadOrderFileHandler
{
    public function __construct(
        private OrderFileRepository $repository,
        private YandexDiskClient $yandexDiskClient,
        private Client $client,
    ) {}

    public function handle(DownloadOrderFileCommand $command): DownloadOrderFileResult
    {
        $orderFile = $this->repository->getById($command->id);
        $href = $this->yandexDiskClient->download($orderFile->diskPath);
        $downloadResponse = $this->client->request('GET', $href, ['http_errors' => false]);

        if ($downloadResponse->getStatusCode() !== 200) {
            throw new DomainExceptionModule(
                module: 'yandex_disk',
                message: 'error.yandex_disk.download_link_failed',
                code: 1,
            );
        }

        return new DownloadOrderFileResult(
            fileName: $orderFile->originalName ?: $orderFile->fileName,
            contentType: $downloadResponse->getHeaderLine('Content-Type') ?: 'application/octet-stream',
            content: (string)$downloadResponse->getBody(),
        );
    }
}
