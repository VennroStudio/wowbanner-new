<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Client;

use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Router\Route;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Client\Command\Client\Delete\DeleteClientCommand;
use App\Modules\Client\Command\Client\Delete\DeleteClientHandler;
use JsonException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Delete(
    path: '/clients/delete/{id}',
    description: 'Полное удаление клиента и всех связанных контактов и компаний',
    summary: 'Удалить клиента',
    security: [['bearerAuth' => []]],
    tags: ['Clients'],
    parameters: [
        new OA\Parameter(name: 'id', description: 'ID клиента', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Клиент удален'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
        new OA\Response(response: 404, description: 'Клиент не найден'),
    ]
)]
final readonly class DeleteClientAction implements RequestHandlerInterface
{
    public function __construct(
        private DeleteClientHandler $handler,
        private Denormalizer $denormalizer,
        private Validator $validator,
    ) {}

    /**
     * @throws ExceptionInterface
     * @throws JsonException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = RequestIdentity::get($request);

        $command = $this->denormalizer->denormalize([
            'id'              => Route::getArgumentToInt($request, 'id'),
            'currentUserId'   => $identity->id,
            'currentUserRole' => $identity->role->value,
        ], DeleteClientCommand::class);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse(1, 200);
    }
}
