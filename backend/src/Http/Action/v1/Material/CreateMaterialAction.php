<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Material;

use App\Components\Exception\AccessDeniedException;
use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Request\RequestFile;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Material\Command\Material\Create\CreateMaterialCommand;
use App\Modules\Material\Command\Material\Create\CreateMaterialHandler;
use App\Modules\Material\Command\Material\MaterialImageItem;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Post(
// ... (OpenAPI continues)
    path: '/materials/create',
    description: 'Создание материала (только администратор). Поддерживает загрузку нескольких изображений (multipart/form-data).',
    summary: 'Создать материал',
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Баннер'),
                    new OA\Property(property: 'description', type: 'string', example: 'Описание материала'),
                    new OA\Property(
                        property: 'images[]',
                        description: 'Массив файлов изображений',
                        type: 'array',
                        items: new OA\Items(type: 'string', format: 'binary'),
                    ),
                    new OA\Property(
                        property: 'imageAlts[]',
                        description: 'Массив альтернативных текстов для изображений (в том же порядке)',
                        type: 'array',
                        items: new OA\Items(type: 'string', example: 'Красивый баннер'),
                    ),
                ]
            )
        )
    ),
    tags: ['Materials'],
    responses: [
        new OA\Response(response: 201, description: 'Материал создан'),
        new OA\Response(response: 401, description: 'Не авторизован'),
        new OA\Response(response: 403, description: 'Доступ запрещён'),
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class CreateMaterialAction implements RequestHandlerInterface
{
    public function __construct(
        private Denormalizer $denormalizer,
        private Validator $validator,
        private CreateMaterialHandler $handler,
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

        $images = RequestFile::extractItems(
            request: $request,
            fileKey: 'images',
            metaKey: 'imageAlts',
            itemClass: MaterialImageItem::class,
            body: $body,
        );

        $command = $this->denormalizer->denormalize(
            array_merge($body, [
                'currentUserId'   => $identity->id,
                'currentUserRole' => $identity->role->value,
                'images'          => $images,
            ]),
            CreateMaterialCommand::class,
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse();
    }
}
