<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Material;

use App\Components\Exception\AccessDeniedException;
use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Router\Route;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Material\Command\Material\Update\UpdateMaterialCommand;
use App\Modules\Material\Command\Material\Update\UpdateMaterialHandler;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Patch(
    path: '/materials/update/{id}',
    description: 'Обновление материала',
    summary: 'Обновить материал',
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Баннер'),
                new OA\Property(property: 'description', type: 'string', example: 'Новое описание'),
                new OA\Property(
                    property: 'options',
                    type: 'array',
                    items: new OA\Items(
                        required: ['name', 'pricingType'],
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', nullable: true),
                            new OA\Property(property: 'name', type: 'string', example: 'Вариант 1'),
                            new OA\Property(property: 'pricingType', type: 'integer', example: 1),
                            new OA\Property(property: 'isCut', type: 'boolean', default: false),
                            new OA\Property(
                                property: 'pricingByArea',
                                type: 'array',
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', nullable: true),
                                        new OA\Property(property: 'dpiType', type: 'integer', example: 1),
                                        new OA\Property(property: 'areaRangeType', type: 'integer', example: 1),
                                        new OA\Property(property: 'price', type: 'string', example: '10.00'),
                                        new OA\Property(property: 'cost', type: 'string', example: '5.00'),
                                        new OA\Property(property: 'printHours', type: 'string', example: '1.00'),
                                    ],
                                    type: 'object',
                                )
                            ),
                            new OA\Property(
                                property: 'pricingByPiece',
                                type: 'array',
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', nullable: true),
                                        new OA\Property(property: 'variantType', type: 'integer', example: 1),
                                        new OA\Property(property: 'price', type: 'string', example: '10.00'),
                                        new OA\Property(property: 'cost', type: 'string', example: '5.00'),
                                        new OA\Property(property: 'printHours', type: 'string', example: '1.00'),
                                    ],
                                    type: 'object',
                                )
                            ),
                            new OA\Property(
                                property: 'pricingByCut',
                                type: 'array',
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', nullable: true),
                                        new OA\Property(property: 'type', type: 'integer', example: 1),
                                        new OA\Property(property: 'price', type: 'string', example: '2.00'),
                                    ],
                                    type: 'object',
                                )
                            ),
                            new OA\Property(
                                property: 'processings',
                                type: 'array',
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', nullable: true),
                                        new OA\Property(property: 'processingId', type: 'integer', example: 1),
                                    ],
                                    type: 'object',
                                )
                            ),
                        ],
                        type: 'object',
                    )
                ),
            ]
        )
    ),
    tags: ['Materials'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            description: 'ID материала',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Материал обновлён'),
        new OA\Response(response: 401, description: 'Не авторизован'),
        new OA\Response(response: 403, description: 'Доступ запрещён'),
        new OA\Response(response: 404, description: 'Материал не найден'),
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class UpdateMaterialAction implements RequestHandlerInterface
{
    public function __construct(
        private Denormalizer $denormalizer,
        private Validator $validator,
        private UpdateMaterialHandler $handler,
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

        $command = $this->denormalizer->denormalize(
            array_merge((array)$request->getParsedBody(), [
                'materialId'      => Route::getArgumentToInt($request, 'id'),
                'currentUserId'   => $identity->id,
                'currentUserRole' => $identity->role->value,
            ]),
            UpdateMaterialCommand::class,
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse(1, 200);
    }
}
