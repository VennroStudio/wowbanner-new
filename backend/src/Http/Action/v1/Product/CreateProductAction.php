<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Product;

use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Product\Command\Product\Create\CreateProductCommand;
use App\Modules\Product\Command\Product\Create\CreateProductHandler;
use JsonException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Post(
    path: '/products/create',
    description: 'Создание продукта с набором материалов и печатей',
    summary: 'Создать продукт',
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Пример продукта'),
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
    tags: ['Products'],
    responses: [
        new OA\Response(response: 201, description: 'Продукт создан'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class CreateProductAction implements RequestHandlerInterface
{
    public function __construct(
        private CreateProductHandler $handler,
        private Denormalizer         $denormalizer,
        private Validator            $validator,
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
            CreateProductCommand::class,
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse();
    }
}
