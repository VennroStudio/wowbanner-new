<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Client;

use App\Components\Http\Request\RequestIdentity;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Http\Route\Route;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Client\Command\Client\Update\UpdateClientCommand;
use App\Modules\Client\Command\Client\Update\UpdateClientHandler;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Patch(
    path: '/clients/update/{id}',
    description: 'Обновление данных клиента, включая контакты и компании (синхронизация коллекций)',
    summary: 'Обновить клиента',
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['lastName', 'firstName', 'docs', 'type'],
            properties: [
                new OA\Property(property: 'lastName', type: 'string', example: 'Иванов'),
                new OA\Property(property: 'firstName', type: 'string', example: 'Иван'),
                new OA\Property(property: 'middleName', type: 'string', example: 'Иванович'),
                new OA\Property(property: 'email', type: 'string', example: 'ivanov@example.com'),
                new OA\Property(property: 'docs', type: 'integer', example: 1),
                new OA\Property(property: 'type', type: 'integer', example: 1),
                new OA\Property(property: 'info', type: 'string', example: 'Обновленная информация'),
                new OA\Property(
                    property: 'phones',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'type', type: 'integer', example: 1),
                            new OA\Property(property: 'phone', type: 'string', example: '+79991234567'),
                        ]
                    )
                ),
                new OA\Property(
                    property: 'companies',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'ООО Ромашка'),
                        ]
                    )
                ),
            ]
        )
    ),
    tags: ['Clients'],
    parameters: [
        new OA\Parameter(name: 'id', description: 'ID клиента', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Клиент обновлен'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class UpdateClientAction implements RequestHandlerInterface
{
    public function __construct(
        private UpdateClientHandler $handler,
        private Denormalizer $denormalizer,
        private Validator $validator,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = RequestIdentity::get($request);

        $command = $this->denormalizer->denormalize(
            array_merge((array)$request->getParsedBody(), [
                'id'              => Route::getArgumentToInt($request, 'id'),
                'currentUserId'   => $identity->id,
                'currentUserRole' => $identity->role->value,
            ]),
            UpdateClientCommand::class
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse(1, 200);
    }
}
