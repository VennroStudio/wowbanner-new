# Enum

Правила для enum-полей сущностей, API-справочников и внутренних доменных состояний.

**Расположение:** `src/Modules/{Module}/Entity/{Entity}/Fields/Enums/{EnumName}.php`

---

## Правила

- Enum, который хранится в БД, должен быть `int`-backed.
- Doctrine-колонка для enum в БД: `#[ORM\Column(type: Types::INTEGER, enumType: SomeEnum::class)]`.
- Enum, который отдается во frontend как справочник `{ id, label }`, должен реализовывать `App\Components\Enum\EnumInterface`.
- API-справочники отдаются через `EnumModel::fromEnumClass(...)`.
- Если набор значений зависит от роли пользователя, enum дополнительно реализует `App\Components\Enum\RoleAwareEnumInterface`.
- Логика доступных значений по роли хранится в enum через `casesForRole(UserRole $role)`, а не в Action.
- Enum только для внутренней доменной логики может быть `string`-backed и не обязан реализовывать `EnumInterface`.
- Все зависимости импортируются через `use`.

---

## Enum для колонки БД

Используется для полей сущности, которые сохраняются в таблицу.

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Entity\{Entity}\Fields\Enums;

use App\Components\Enum\EnumInterface;

enum {EnumName}: int implements EnumInterface
{
    case ACTIVE = 1;
    case INACTIVE = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE   => 'Активен',
            self::INACTIVE => 'Неактивен',
        };
    }
}
```

Пример поля в Entity:

```php
#[ORM\Column(type: Types::INTEGER, enumType: {EnumName}::class)]
private(set) {EnumName} $status;
```

---

## Enum для API-справочника

Если frontend должен получить список значений в формате `{ id, label }`, Action возвращает данные через `EnumModel`.

```php
return new JsonDataResponse(EnumModel::fromEnumClass({EnumName}::class));
```

Сам enum:

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Entity\{Entity}\Fields\Enums;

use App\Components\Enum\EnumInterface;

enum {EnumName}: int implements EnumInterface
{
    case ONE = 1;
    case TWO = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::ONE => 'Первый',
            self::TWO => 'Второй',
        };
    }
}
```

---

## Enum-справочник с учетом роли

Если набор значений зависит от роли текущего пользователя, фильтрация выполняется внутри enum.

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Entity\{Entity}\Fields\Enums;

use App\Components\Enum\EnumInterface;
use App\Components\Enum\RoleAwareEnumInterface;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

enum {EnumName}: int implements EnumInterface, RoleAwareEnumInterface
{
    case ONE = 1;
    case TWO = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::ONE => 'Первый',
            self::TWO => 'Второй',
        };
    }

    /**
     * @return list<self>
     */
    public static function casesForRole(UserRole $role): array
    {
        return match ($role) {
            UserRole::ADMIN => self::cases(),
            default => [self::ONE],
        };
    }
}
```

Action для такого справочника:

```php
$identity = RequestIdentity::get($request);

return new JsonDataResponse(
    EnumModel::fromEnumClassForRole({EnumName}::class, $identity->role),
);
```

---

## Enum только для доменной логики

Если enum не хранится в БД и не отдается как API-справочник, можно использовать `string`-backed enum без `getLabel()`.

```php
enum {EnumName}: string
{
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case USED = 'used';
    case REVOKED = 'revoked';
}
```
