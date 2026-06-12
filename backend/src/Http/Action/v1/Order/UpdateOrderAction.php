<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Order;

use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Request\RequestFile;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Router\Route;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Order\Command\Order\Update\UpdateOrderCommand;
use App\Modules\Order\Command\Order\Update\UpdateOrderHandler;
use App\Modules\Order\ReadModel\OrderFile\OrderFileItem;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Patch(
    path: '/orders/update/{id}',
    description: 'Обновление заказа со синхронизацией вложенных сущностей',
    summary: 'Обновить заказ',
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['clientId', 'statusType', 'storageType', 'acceptedAt', 'deadlineAt'],
                properties: [
                    new OA\Property(property: 'clientId', type: 'integer', example: 1),
                    new OA\Property(property: 'managerId', type: 'integer', nullable: true, example: 2),
                    new OA\Property(property: 'designerId', type: 'integer', nullable: true, example: 3),
                    new OA\Property(property: 'statusType', type: 'integer', example: 1),
                    new OA\Property(property: 'storageType', type: 'integer', example: 1),
                    new OA\Property(property: 'acceptedAt', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'deadlineAt', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'generalNote', type: 'string', nullable: true),
                    new OA\Property(property: 'extension', type: 'string', nullable: true),
                    new OA\Property(property: 'delivery', type: 'object', nullable: true),
                    new OA\Property(
                        property: 'keepFileIds',
                        description: 'ID уже загруженных файлов, которые нужно оставить у заказа',
                        type: 'array',
                        items: new OA\Items(type: 'integer')
                    ),
                    new OA\Property(property: 'items', type: 'array', items: new OA\Items(type: 'object')),
                    new OA\Property(property: 'millings', type: 'array', items: new OA\Items(type: 'object')),
                    new OA\Property(property: 'payments', type: 'array', items: new OA\Items(type: 'object')),
                    new OA\Property(property: 'sections', type: 'array', items: new OA\Items(type: 'object')),
                    new OA\Property(property: 'services', type: 'array', items: new OA\Items(type: 'object')),
                    new OA\Property(
                        property: 'fileOriginalNames',
                        description: 'Необязательные отображаемые имена для новых файлов в том же порядке',
                        type: 'array',
                        items: new OA\Items(type: 'string')
                    ),
                    new OA\Property(
                        property: 'files[]',
                        description: 'Файлы заказа для загрузки или замены',
                        type: 'array',
                        items: new OA\Items(type: 'string', format: 'binary')
                    ),
                ]
            )
        )
    ),
    tags: ['Orders'],
    parameters: [
        new OA\Parameter(name: 'id', description: 'ID заказа', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Заказ обновлён'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class UpdateOrderAction implements RequestHandlerInterface
{
    public function __construct(
        private UpdateOrderHandler $handler,
        private Denormalizer $denormalizer,
        private Validator $validator,
    ) {}

    /**
     * @throws ExceptionInterface
     * @throws JsonException
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = RequestIdentity::get($request);
        $body = (array)$request->getParsedBody();
        $files = RequestFile::extractItems(
            request: $request,
            fileKey: 'files',
            metaKey: 'fileOriginalNames',
            itemClass: OrderFileItem::class,
            body: $body,
        );

        $command = $this->denormalizer->denormalize(
            array_merge($body, [
                'id'              => Route::getArgumentToInt($request, 'id'),
                'currentUserId'   => $identity->id,
                'currentUserRole' => $identity->role->value,
                'files'           => $files,
                'keepFileIds'     => $this->extractKeepFileIds($body),
            ]),
            UpdateOrderCommand::class,
        );

        $this->validator->validate($command);
        $this->handler->handle($command);

        return new JsonDataSuccessResponse(1, 200);
    }

    /**
     * @param array<string, mixed> $body
     * @return list<int>|null
     */
    private function extractKeepFileIds(array $body): ?array
    {
        if (!\array_key_exists('keepFileIds', $body)) {
            return null;
        }

        return array_map('intval', array_values((array)$body['keepFileIds']));
    }
}
