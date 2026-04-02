<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Printing;

use App\Components\Exception\AccessDeniedException;
use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Router\Route;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Printing\Command\Printing\Delete\DeletePrintingCommand;
use App\Modules\Printing\Command\Printing\Delete\DeletePrintingHandler;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Delete(
    path: '/printings/delete/{id}',
    description: 'Удаление записи печати (только администратор)',
    summary: 'Удалить печать',
    security: [['bearerAuth' => []]],
    tags: ['Printings'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            description: 'ID записи печати',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Запись удалена'),
        new OA\Response(response: 401, description: 'Не авторизован'),
        new OA\Response(response: 403, description: 'Доступ запрещён'),
        new OA\Response(response: 404, description: 'Запись не найдена'),
    ]
)]
final readonly class DeletePrintingAction implements RequestHandlerInterface
{
    public function __construct(
        private Denormalizer $denormalizer,
        private Validator $validator,
        private DeletePrintingHandler $handler,
    ) {}

    /**
     * @throws ExceptionInterface
     * @throws AccessDeniedException
     * @throws JsonException
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = RequestIdentity::get($request);

        $command = $this->denormalizer->denormalize([
            'printingId'      => Route::getArgumentToInt($request, 'id'),
            'currentUserId'   => $identity->id,
            'currentUserRole' => $identity->role->value,
        ], DeletePrintingCommand::class);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse(1, 200);
    }
}
