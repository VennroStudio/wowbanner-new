<?php

declare(strict_types=1);

namespace App\Http\Action\v1\User\Admin;

use App\Components\Exception\AccessDeniedException;
use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Router\Route;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\User\Command\User\AdminUpdate\AdminUpdateUserCommand;
use App\Modules\User\Command\User\AdminUpdate\AdminUpdateUserHandler;
use DateMalformedStringException;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Patch(
    path: '/admin/users/update/{id}',
    description: 'Обновление данных профиля пользователя администратором',
    summary: 'Обновить профиль пользователя',
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['lastName', 'firstName', 'email', 'role'],
            properties: [
                new OA\Property(property: 'role', type: 'integer', example: 1),
                new OA\Property(property: 'lastName', type: 'string', example: 'Петросян'),
                new OA\Property(property: 'firstName', type: 'string', example: 'Артем'),
                new OA\Property(property: 'email', type: 'string', example: 'vennro@gmail.com')
            ]
        )
    ),
    tags: ['Admin'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            description: 'ID пользователя',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Профиль обновлён'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
        new OA\Response(response: 403, description: 'Доступ запрещен'),
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class AdminUpdateUserAction implements RequestHandlerInterface
{
    public function __construct(
        private Denormalizer $denormalizer,
        private Validator $validator,
        private AdminUpdateUserHandler $handler,
    ) {}

    /**
     * @throws DateMalformedStringException
     * @throws ExceptionInterface
     * @throws AccessDeniedException
     * @throws JsonException
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userId = Route::getArgumentToInt($request, 'id');
        $identity = RequestIdentity::get($request);

        $command = $this->denormalizer->denormalize(
            array_merge((array)$request->getParsedBody(), [
                'userId'          => $userId,
                'currentUserId'   => $identity->id,
                'currentUserRole' => $identity->role->value,
            ]),
            AdminUpdateUserCommand::class,
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse(1, 200);
    }
}
