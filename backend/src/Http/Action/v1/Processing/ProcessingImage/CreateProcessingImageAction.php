<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Processing\ProcessingImage;

use App\Components\Exception\AccessDeniedException;
use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Request\RequestFile;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Router\Route;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Processing\Command\ProcessingImage\Create\CreateProcessingImageCommand;
use App\Modules\Processing\Command\ProcessingImage\Create\CreateProcessingImageHandler;
use App\Modules\Processing\ReadModel\ProcessingImage\ProcessingImageItem;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Random\RandomException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Post(
    path: '/processings/{id}/images',
    description: 'Добавление изображений к обработке',
    summary: 'Добавить изображения к обработке',
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                properties: [
                    new OA\Property(
                        property: 'images[]',
                        description: 'Массив файлов изображений',
                        type: 'array',
                        items: new OA\Items(type: 'string', format: 'binary'),
                    ),
                    new OA\Property(
                        property: 'imageAlts[]',
                        description: 'Массив альтернативных текстов (в том же порядке)',
                        type: 'array',
                        items: new OA\Items(type: 'string', example: 'Люверс на баннере'),
                    ),
                ]
            )
        )
    ),
    tags: ['Processings'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            description: 'ID обработки',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Изображения добавлены'),
        new OA\Response(response: 401, description: 'Не авторизован'),
        new OA\Response(response: 403, description: 'Доступ запрещён'),
        new OA\Response(response: 404, description: 'Обработка не найдена'),
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class CreateProcessingImageAction implements RequestHandlerInterface
{
    public function __construct(
        private Denormalizer $denormalizer,
        private Validator $validator,
        private CreateProcessingImageHandler $handler,
    ) {}

    /**
     * @throws ExceptionInterface|RandomException|AccessDeniedException
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
            itemClass: ProcessingImageItem::class,
            body: $body,
        );

        $command = $this->denormalizer->denormalize(
            array_merge($body, [
                'processingId'    => Route::getArgumentToInt($request, 'id'),
                'currentUserId'   => $identity->id,
                'currentUserRole' => $identity->role->value,
                'images'          => $images,
            ]),
            CreateProcessingImageCommand::class,
        );

        $this->validator->validate($command);
        $this->handler->handle($command);

        return new JsonDataSuccessResponse();
    }
}
