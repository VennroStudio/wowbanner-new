<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Production;

use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Router\Route;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Production\Command\Production\Delete\DeleteProductionCommand;
use App\Modules\Production\Command\Production\Delete\DeleteProductionHandler;
use JsonException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Delete(
    path: '/productions/delete/{id}',
    description: 'Полное удаление производства и связанных записей материалов и печатей',
    summary: 'Удалить производство',
    security: [['bearerAuth' => []]],
    tags: ['Productions'],
    parameters: [
        new OA\Parameter(name: 'id', description: 'ID производства', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Производство удалено'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
        new OA\Response(response: 404, description: 'Производство не найдено'),
    ]
)]
final readonly class DeleteProductionAction implements RequestHandlerInterface
{
    public function __construct(
        private DeleteProductionHandler $handler,
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
        ], DeleteProductionCommand::class);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse(1, 200);
    }
}
