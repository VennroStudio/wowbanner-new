<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Production;

use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Production\Command\Production\Create\CreateProductionCommand;
use App\Modules\Production\Command\Production\Create\CreateProductionHandler;
use JsonException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Post(
    path: '/productions/create',
    description: 'Создание производства с набором материалов и печатей',
    summary: 'Создать производство',
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Цех №1'),
                new OA\Property(
                    property: 'materials',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', nullable: true),
                            new OA\Property(property: 'materialOptionId', type: 'integer', example: 1),
                        ]
                    )
                ),
                new OA\Property(
                    property: 'prints',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', nullable: true),
                            new OA\Property(property: 'printId', type: 'integer', example: 1),
                        ]
                    )
                ),
            ]
        )
    ),
    tags: ['Productions'],
    responses: [
        new OA\Response(response: 201, description: 'Производство создано'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class CreateProductionAction implements RequestHandlerInterface
{
    public function __construct(
        private CreateProductionHandler $handler,
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

        $command = $this->denormalizer->denormalize(
            array_merge((array)$request->getParsedBody(), [
                'currentUserId'   => $identity->id,
                'currentUserRole' => $identity->role->value,
            ]),
            CreateProductionCommand::class,
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse();
    }
}
