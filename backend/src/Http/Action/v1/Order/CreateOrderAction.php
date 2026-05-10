<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Order;

use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Request\RequestFile;
use App\Components\Http\Response\JsonDataSuccessResponse;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Order\Command\Order\Create\CreateOrderCommand;
use App\Modules\Order\Command\Order\Create\CreateOrderHandler;
use App\Modules\Order\ReadModel\OrderFile\OrderFileItem;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Post(
    path: '/orders/create',
    description: 'Создание нового заказа вместе с доставкой, файлами, позициями, платежами, секциями и услугами',
    summary: 'Создать заказ',
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['clientId', 'statusType', 'storageType', 'acceptedAt', 'deadlineAt'],
                properties: [
                    new OA\Property(property: 'clientId', type: 'integer', example: 1),
                    new OA\Property(property: 'managerId', type: 'integer', example: 2, nullable: true),
                    new OA\Property(property: 'designerId', type: 'integer', example: 3, nullable: true),
                    new OA\Property(property: 'statusType', type: 'integer', example: 1),
                    new OA\Property(property: 'storageType', type: 'integer', example: 1),
                    new OA\Property(property: 'acceptedAt', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'deadlineAt', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'generalNote', type: 'string', nullable: true),
                    new OA\Property(property: 'extension', type: 'string', nullable: true),
                    new OA\Property(
                        property: 'delivery',
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', nullable: true),
                            new OA\Property(property: 'deliveryType', type: 'integer'),
                            new OA\Property(property: 'address', type: 'string', nullable: true),
                            new OA\Property(property: 'comment', type: 'string', nullable: true),
                        ],
                        type: 'object',
                        nullable: true
                    ),
                    new OA\Property(
                        property: 'items',
                        description: 'Массив печатных позиций заказа',
                        type: 'array',
                        items: new OA\Items(type: 'object')
                    ),
                    new OA\Property(
                        property: 'millings',
                        description: 'Массив позиций фрезеровки',
                        type: 'array',
                        items: new OA\Items(type: 'object')
                    ),
                    new OA\Property(
                        property: 'payments',
                        type: 'array',
                        items: new OA\Items(type: 'object')
                    ),
                    new OA\Property(
                        property: 'sections',
                        type: 'array',
                        items: new OA\Items(type: 'object')
                    ),
                    new OA\Property(
                        property: 'services',
                        type: 'array',
                        items: new OA\Items(type: 'object')
                    ),
                    new OA\Property(
                        property: 'fileOriginalNames',
                        description: 'Необязательные отображаемые имена для новых файлов в том же порядке',
                        type: 'array',
                        items: new OA\Items(type: 'string')
                    ),
                    new OA\Property(
                        property: 'files[]',
                        description: 'Файлы заказа',
                        type: 'array',
                        items: new OA\Items(type: 'string', format: 'binary')
                    ),
                ]
            )
        )
    ),
    tags: ['Orders'],
    responses: [
        new OA\Response(response: 201, description: 'Заказ создан'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class CreateOrderAction implements RequestHandlerInterface
{
    public function __construct(
        private CreateOrderHandler $handler,
        private Denormalizer $denormalizer,
        private Validator $validator,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = RequestIdentity::get($request);
        $body = (array) $request->getParsedBody();
        $files = RequestFile::extractItems(
            request: $request,
            fileKey: 'files',
            metaKey: 'fileOriginalNames',
            itemClass: OrderFileItem::class,
            body: $body,
        );

        $command = $this->denormalizer->denormalize(
            array_merge($body, [
                'currentUserId' => $identity->id,
                'currentUserRole' => $identity->role->value,
                'files' => $files,
            ]),
            CreateOrderCommand::class,
        );

        $this->validator->validate($command);
        $this->handler->handle($command);

        return new JsonDataSuccessResponse();
    }
}
