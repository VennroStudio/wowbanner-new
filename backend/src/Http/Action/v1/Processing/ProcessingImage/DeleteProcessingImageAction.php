<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Processing\ProcessingImage;

use App\Components\Exception\AccessDeniedException;
use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Http\Route\Route;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Processing\Command\ProcessingImage\Delete\DeleteProcessingImageCommand;
use App\Modules\Processing\Command\ProcessingImage\Delete\DeleteProcessingImageHandler;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Delete(
    path: '/processings/images/{imageId}',
    description: 'Удаление изображения обработки',
    summary: 'Удалить изображение обработки',
    security: [['bearerAuth' => []]],
    tags: ['Processings'],
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
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class DeleteProcessingImageAction implements RequestHandlerInterface
{
    public function __construct(
        private Denormalizer $denormalizer,
        private Validator $validator,
        private DeleteProcessingImageHandler $handler,
    ) {}

    /** @throws ExceptionInterface|AccessDeniedException */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = RequestIdentity::get($request);

        $command = $this->denormalizer->denormalize([
            'id'              => Route::getArgumentToInt($request, 'imageId'),
            'currentUserId'   => $identity->id,
            'currentUserRole' => $identity->role->value,
        ], DeleteProcessingImageCommand::class);

        $this->validator->validate($command);
        $this->handler->handle($command);

        return new JsonDataSuccessResponse(1, 200);
    }
}
