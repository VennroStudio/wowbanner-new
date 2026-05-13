<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Order;

use App\Components\Exception\DomainExceptionModule;
use App\Components\Router\Route;
use GuzzleHttp\Client;
use App\Modules\Order\Entity\OrderFile\OrderFileRepository;
use App\Modules\Order\Service\OrderFileStorageService;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;

final readonly class GetOrderFileDownloadAction implements RequestHandlerInterface
{
    public function __construct(
        private OrderFileRepository $repository,
        private OrderFileStorageService $storageService,
        private Client $client,
    ) {}

    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $orderFile = $this->repository->getById(Route::getArgumentToInt($request, 'id'));
        $href = $this->storageService->download($orderFile->diskPath);
        $downloadResponse = $this->client->request('GET', $href, ['http_errors' => false]);

        if ($downloadResponse->getStatusCode() !== 200) {
            throw new DomainExceptionModule(
                module: 'yandex_disk',
                message: 'error.yandex_disk.download_link_failed',
                code: 1,
            );
        }

        $fileName = $this->escapeFileName($orderFile->originalName ?: $orderFile->fileName);
        $contentType = $downloadResponse->getHeaderLine('Content-Type') ?: 'application/octet-stream';

        return new Response(
            200,
            new Headers([
                'Content-Type' => $contentType,
                'Content-Disposition' => sprintf(
                    'attachment; filename="%s"; filename*=UTF-8\'\'%s',
                    $fileName,
                    rawurlencode($fileName),
                ),
            ]),
            new StreamFactory()->createStream((string) $downloadResponse->getBody()),
        );
    }

    private function escapeFileName(string $fileName): string
    {
        return str_replace(['\\', '"'], '', $fileName);
    }
}
