<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Material;

use App\Components\Exception\AccessDeniedException;
use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Request\RequestFile;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Router\Route;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Material\Command\Material\MaterialImageItem;
use App\Modules\Material\Command\Material\Update\UpdateMaterialCommand;
use App\Modules\Material\Command\Material\Update\UpdateMaterialHandler;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Patch(
// ... (OpenAPI continues)
    path: '/materials/update/{id}',
    description: 'Обновление материала (только администратор). Поддерживает добавление новых изображений и удаление существующих.',
    summary: 'Обновить материал',
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Баннер'),
                    new OA\Property(property: 'description', type: 'string', example: 'Новое описание'),
                    new OA\Property(
                        property: 'newImages[]',
                        description: 'Массив новых файлов изображений',
                        type: 'array',
                        items: new OA\Items(type: 'string', format: 'binary'),
                    ),
                    new OA\Property(
                        property: 'newImageAlts[]',
                        description: 'Массив альтернативных текстов для новых изображений',
                        type: 'array',
                        items: new OA\Items(type: 'string', example: 'Красивый баннер'),
                    ),
                    new OA\Property(
                        property: 'imagesToDelete[]',
                        description: 'Массив ID изображений для удаления',
                        type: 'array',
                        items: new OA\Items(type: 'integer', example: 1),
                    ),
                ]
            )
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
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = RequestIdentity::get($request);
        $body = (array)$request->getParsedBody();

        $newImages = RequestFile::extractItems(
            request: $request,
            fileKey: 'newImages',
            metaKey: 'newImageAlts',
            itemClass: MaterialImageItem::class,
            body: $body,
        );

        $imagesToDelete = RequestFile::extractIds('imagesToDelete', $body);

        $command = $this->denormalizer->denormalize(
            array_merge($body, [
                'materialId'      => Route::getArgumentToInt($request, 'id'),
                'currentUserId'   => $identity->id,
                'currentUserRole' => $identity->role->value,
                'newImages'       => $newImages,
                'imagesToDelete'  => $imagesToDelete,
            ]),
            UpdateMaterialCommand::class,
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse(1, 200);
    }
}
