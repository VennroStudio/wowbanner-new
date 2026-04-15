<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Product;

use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Router\Route;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Product\Command\Product\Update\UpdateProductCommand;
use App\Modules\Product\Command\Product\Update\UpdateProductHandler;
use JsonException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Patch(
    path: '/products/update/{id}',
    description: 'Обновление продукта и синхронизация списков материалов и печатей',
    summary: 'Обновить продукт',
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
    parameters: [
        new OA\Parameter(name: 'id', description: 'ID продукта', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Продукт обновлён'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class UpdateProductAction implements RequestHandlerInterface
{
    public function __construct(
        private UpdateProductHandler $handler,
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
                'id'              => Route::getArgumentToInt($request, 'id'),
                'currentUserId'   => $identity->id,
                'currentUserRole' => $identity->role->value,
            ]),
            UpdateProductCommand::class,
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse(1, 200);
    }
}
