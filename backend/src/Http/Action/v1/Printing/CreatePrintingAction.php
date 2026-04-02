<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Printing;

use App\Components\Exception\AccessDeniedException;
use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Printing\Command\Printing\Create\CreatePrintingCommand;
use App\Modules\Printing\Command\Printing\Create\CreatePrintingHandler;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Post(
    path: '/printings/create',
    description: 'Создание записи печати (только администратор)',
    summary: 'Создать печать',
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'УФ-печать'),
            ]
        )
    ),
    tags: ['Printings'],
    responses: [
        new OA\Response(response: 201, description: 'Запись создана'),
        new OA\Response(response: 401, description: 'Не авторизован'),
        new OA\Response(response: 403, description: 'Доступ запрещён'),
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class CreatePrintingAction implements RequestHandlerInterface
{
    public function __construct(
        private Denormalizer $denormalizer,
        private Validator $validator,
        private CreatePrintingHandler $handler,
    ) {}

    /**
     * @throws ExceptionInterface
     * @throws AccessDeniedException
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = RequestIdentity::get($request);

        $command = $this->denormalizer->denormalize(
            array_merge((array)$request->getParsedBody(), [
                'currentUserId'   => $identity->id,
                'currentUserRole' => $identity->role->value,
            ]),
            CreatePrintingCommand::class,
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse();
    }
}
