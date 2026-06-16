<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Order;

use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Router\Route;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Order\Command\OrderFile\Delete\DeleteOrderFileCommand;
use App\Modules\Order\Command\OrderFile\Delete\DeleteOrderFileHandler;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Delete(
    path: '/orders/files/{id}',
    description: 'Удаление файла заказа',
    summary: 'Удалить файл заказа',
    security: [['bearerAuth' => []]],
    tags: ['Orders'],
    parameters: [
        new OA\Parameter(name: 'id', description: 'ID файла заказа', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Файл удалён'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
        new OA\Response(response: 403, description: 'Недостаточно прав'),
        new OA\Response(response: 404, description: 'Файл не найден'),
    ]
)]
final readonly class DeleteOrderFileAction implements RequestHandlerInterface
{
    public function __construct(
        private DeleteOrderFileHandler $handler,
        private Denormalizer $denormalizer,
        private Validator $validator,
    ) {}

    /**
     * @throws ExceptionInterface
     * @throws JsonException
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = RequestIdentity::get($request);

        $command = $this->denormalizer->denormalize([
            'currentUserId'   => $identity->id,
            'currentUserRole' => $identity->role->value,
            'id'              => Route::getArgumentToInt($request, 'id'),
        ], DeleteOrderFileCommand::class);

        $this->validator->validate($command);
        $this->handler->handle($command);

        return new JsonDataSuccessResponse(1, 200);
    }
}
