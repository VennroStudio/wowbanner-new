<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Order;

use App\Components\Http\Response\FileDownloadResponse;
use App\Components\Router\Route;
use App\Modules\Order\Command\OrderFile\Download\DownloadOrderFileCommand;
use App\Modules\Order\Command\OrderFile\Download\DownloadOrderFileHandler;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
