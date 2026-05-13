<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Order;

use App\Components\Flusher\FlusherInterface;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Router\Route;
use App\Modules\Order\Command\OrderFile\Delete\DeleteOrderFileCommand;
use App\Modules\Order\Command\OrderFile\Delete\DeleteOrderFileHandler;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class DeleteOrderFileAction implements RequestHandlerInterface
{
    public function __construct(
        private DeleteOrderFileHandler $handler,
        private FlusherInterface $flusher,
    ) {}

    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->handler->handle(new DeleteOrderFileCommand(
            id: Route::getArgumentToInt($request, 'id'),
        ));
        $this->flusher->flush();

        return new JsonDataSuccessResponse(1, 200);
    }
}
