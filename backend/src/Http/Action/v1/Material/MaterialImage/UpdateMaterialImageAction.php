<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Material\MaterialImage;

use App\Components\Exception\AccessDeniedException;
use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Router\Route;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Material\Command\MaterialImage\Update\UpdateMaterialImageCommand;
use App\Modules\Material\Command\MaterialImage\Update\UpdateMaterialImageHandler;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Patch(
    path: '/materials/images/{imageId}',
    description: 'Обновление alt изображения материала',
    summary: 'Обновить alt изображения',
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'alt', type: 'string', example: 'Красивый баннер', nullable: true),
            ]
        )
    ),
    tags: ['Materials'],
    parameters: [
        new OA\Parameter(
            name: 'imageId',
            description: 'ID изображения',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Alt изображения обновлён'),
        new OA\Response(response: 401, description: 'Не авторизован'),
        new OA\Response(response: 403, description: 'Доступ запрещён'),
        new OA\Response(response: 404, description: 'Изображение не найдено'),
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class UpdateMaterialImageAction implements RequestHandlerInterface
{
    public function __construct(
        private Denormalizer $denormalizer,
        private Validator $validator,
        private UpdateMaterialImageHandler $handler,
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
                'materialImageId' => Route::getArgumentToInt($request, 'imageId'),
                'currentUserId'   => $identity->id,
                'currentUserRole' => $identity->role->value,
            ]),
            UpdateMaterialImageCommand::class,
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse(1, 200);
    }
}
