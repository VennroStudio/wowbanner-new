<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Order;

use App\Components\Http\Response\FileDownloadResponse;
use App\Components\Router\Route;
use App\Modules\Order\Command\OrderFile\Download\DownloadOrderFileCommand;
use App\Modules\Order\Command\OrderFile\Download\DownloadOrderFileHandler;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/orders/files/{id}/download',
    description: 'Скачивание файла заказа',
    summary: 'Скачать файл заказа',
    security: [['bearerAuth' => []]],
    tags: ['Orders'],
    parameters: [
        new OA\Parameter(name: 'id', description: 'ID файла заказа', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Файл заказа',
            content: new OA\MediaType(
                mediaType: 'application/octet-stream',
                schema: new OA\Schema(type: 'string', format: 'binary')
            )
        ),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
        new OA\Response(response: 404, description: 'Файл не найден'),
    ]
)]
final readonly class GetOrderFileDownloadAction implements RequestHandlerInterface
{
    public function __construct(
        private DownloadOrderFileHandler $handler,
    ) {}

    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $file = $this->handler->handle(new DownloadOrderFileCommand(
            id: Route::getArgumentToInt($request, 'id'),
        ));

        return new FileDownloadResponse(
            fileName: $file->fileName,
            contentType: $file->contentType,
            content: $file->content,
        );
    }
}
