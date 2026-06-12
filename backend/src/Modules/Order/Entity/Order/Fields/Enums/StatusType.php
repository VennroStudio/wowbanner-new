<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\Order\Fields\Enums;

use App\Components\Enum\EnumInterface;
use App\Components\Enum\RoleAwareEnumInterface;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;
use Override;

enum StatusType: int implements EnumInterface, RoleAwareEnumInterface
{
    case UNDER_APPROVAL = 1;
    case SENT_TO_PREPRESS = 2;
    case ACCEPTED_FOR_WORK_DESIGNER = 3;
    case PREVIEW_SENT = 4;
    case SENT_TO_PRODUCTION = 5;
    case ACCEPTED_FOR_WORK_PRODUCTION = 6;
    case POST_PRINT_PROCESSING = 7;
    case READY = 8;
    case SHIPPED = 9;

    #[Override]
    public function getLabel(): string
    {
        return match ($this) {
            self::UNDER_APPROVAL               => 'На согласовании',
            self::SENT_TO_PREPRESS             => 'Отправлен в препресс',
            self::ACCEPTED_FOR_WORK_DESIGNER   => 'Принят в работу дизайнером',
            self::PREVIEW_SENT                 => 'Отправлено превью',
            self::SENT_TO_PRODUCTION           => 'Отправлено в производство',
            self::ACCEPTED_FOR_WORK_PRODUCTION => 'Принят в работу на производстве',
            self::POST_PRINT_PROCESSING        => 'После печатная обработка',
            self::READY                        => 'Готов',
            self::SHIPPED                      => 'Отгружен',
        };
    }

    /**
     * Пока текущая матрица ролей заказа явно не зафиксирована,
     * все существующие роли получают полный список статусов.
     *
     * @return list<self>
     */
    public static function casesForRole(UserRole $role): array
    {
        return match ($role) {
            UserRole::ADMIN,
            UserRole::DEVELOPER,
            UserRole::EDITOR,
            UserRole::USER => self::cases(),
        };
    }
}
