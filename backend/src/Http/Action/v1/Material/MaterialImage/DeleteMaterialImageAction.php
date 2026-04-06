<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Material\MaterialImage;

use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Router\Route;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Material\Command\MaterialImage\Delete\DeleteMaterialImageCommand;
use App\Modules\Material\Command\MaterialImage\Delete\DeleteMaterialImageHandler;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Delete(
    path: '/materials/{id}/images/{imageId}',
    description: 'Удаление изображения материала.',
    summary: 'Удалить изображение материала',
    security: [['bearerAuth' => []]],
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
        new OA\Response(response: 200, description: 'Изображение удалено'),
        new OA\Response(response: 401, description: 'Не авторизован'),
        new OA\Response(response: 403, description: 'Доступ запрещён'),
        new OA\Response(response: 404, description: 'Изображение не найдено'),
    ]
)]
final readonly class DeleteMaterialImageAction implements RequestHandlerInterface
{
    public function __construct(
        private Denormalizer $denormalizer,
        private Validator $validator,
        private DeleteMaterialImageHandler $handler,
    ) {}

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ExceptionInterface
     * @throws JsonException
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = RequestIdentity::get($request);

        $command = $this->denormalizer->denormalize([
            'materialImageId'      => Route::getArgumentToInt($request, 'imageId'),
            'currentUserId'   => $identity->id,
            'currentUserRole' => $identity->role->value,
        ], DeleteMaterialImageCommand::class);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse(1, 200);
    }
}
