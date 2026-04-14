<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Processing;

use App\Components\Exception\AccessDeniedException;
use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Router\Route;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Processing\Command\Processing\Delete\DeleteProcessingCommand;
use App\Modules\Processing\Command\Processing\Delete\DeleteProcessingHandler;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Delete(
    path: '/processings/delete/{id}',
    description: 'Удаление обработки',
    summary: 'Удалить обработку',
    security: [['bearerAuth' => []]],
    tags: ['Processings'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            description: 'ID обработки',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        )
    ],
    responses: [
        new OA\Response(response: 200, description: 'Обработка удалена'),
        new OA\Response(response: 401, description: 'Не авторизован'),
        new OA\Response(response: 403, description: 'Доступ запрещён'),
        new OA\Response(response: 404, description: 'Обработка не найдена'),
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class DeleteProcessingAction implements RequestHandlerInterface
{
    public function __construct(
        private Denormalizer $denormalizer,
        private Validator $validator,
        private DeleteProcessingHandler $handler,
    ) {}

    /** @throws AccessDeniedException|ExceptionInterface */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = RequestIdentity::get($request);

        $command = $this->denormalizer->denormalize([
            'id'              => Route::getArgumentToInt($request, 'id'),
            'currentUserId'   => $identity->id,
            'currentUserRole' => $identity->role->value,
        ], DeleteProcessingCommand::class);

        $this->validator->validate($command);
        $this->handler->handle($command);

        return new JsonDataSuccessResponse(1, 200);
    }
}
