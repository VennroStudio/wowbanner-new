<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Processing;

use App\Components\Exception\AccessDeniedException;
use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Processing\Command\Processing\Create\CreateProcessingCommand;
use App\Modules\Processing\Command\Processing\Create\CreateProcessingHandler;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Post(
    path: '/processings/create',
    description: 'Создание обработки',
    summary: 'Создать обработку',
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'type'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Люверсы'),
                new OA\Property(property: 'description', type: 'string', example: 'Установка люверсов по периметру'),
                new OA\Property(property: 'type', type: 'integer', example: 1),
                new OA\Property(property: 'costPrice', type: 'string', example: '10.00'),
                new OA\Property(property: 'price', type: 'string', example: '15.00'),
            ]
        )
    ),
    tags: ['Processings'],
    responses: [
        new OA\Response(response: 201, description: 'Обработка создана'),
        new OA\Response(response: 401, description: 'Не авторизован'),
        new OA\Response(response: 403, description: 'Доступ запрещён'),
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class CreateProcessingAction implements RequestHandlerInterface
{
    public function __construct(
        private Denormalizer $denormalizer,
        private Validator $validator,
        private CreateProcessingHandler $handler,
    ) {}

    /** @throws AccessDeniedException|ExceptionInterface */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = RequestIdentity::get($request);

        $command = $this->denormalizer->denormalize(
            array_merge((array)$request->getParsedBody(), [
                'currentUserId'   => $identity->id,
                'currentUserRole' => $identity->role->value,
            ]),
            CreateProcessingCommand::class,
        );

        $this->validator->validate($command);
        $this->handler->handle($command);

        return new JsonDataSuccessResponse();
    }
}
