# Entity — Доменная сущность

**Расположение:** `src/Modules/{Module}/Entity/{Entity}/{Entity}.php`

---

## Правила

- Класс `class` (не `readonly`, не `final`) — Doctrine требует proxy-объекты
- Конструктор **`private`**, создание только через статический `create()`
- Свойства — **`private(set)`** (чтение публичное, запись только внутри класса)
- ID — `int`, автоинкремент: `#[ORM\GeneratedValue]`
- Даты — `DateTimeImmutable` через `UtcClock::now()`
- Enum в БД — `int`-backed: `#[ORM\Column(type: Types::INTEGER, enumType: SomeEnum::class)]`
- Бизнес-методы изменения состояния возвращают `bool`, если переход может не произойти в штатной ситуации
- `DomainExceptionModule` — только при нарушении бизнес-правила, не для чтения состояния
- Все зависимости импортируются через `use`

---

## Пример: сущность с переходами состояния

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Entity\{Entity};

use App\Components\Clock\UtcClock;
use App\Modules\{Module}\Entity\{Entity}\Fields\Enums\{Entity}State;
use App\Modules\{Module}\Entity\{Entity}\Fields\Enums\{Entity}Type;
use DateMalformedStringException;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '{table_name}')]
class {Entity}
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER, enumType: {Entity}Type::class)]
    private(set) {Entity}Type $type;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private(set) string $hash;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private(set) DateTimeImmutable $expiresAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private(set) ?DateTimeImmutable $usedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private(set) ?DateTimeImmutable $revokedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private(set) DateTimeImmutable $createdAt;

    /** @throws DateMalformedStringException */
    private function __construct(
        {Entity}Type $type,
        string $hash,
        DateTimeImmutable $expiresAt,
    ) {
        $this->type = $type;
        $this->hash = $hash;
        $this->expiresAt = $expiresAt;
        $this->createdAt = UtcClock::now();
    }

    /** @throws DateMalformedStringException */
    public static function create(
        {Entity}Type $type,
        string $hash,
        DateTimeImmutable $expiresAt,
    ): self {
        return new self($type, $hash, $expiresAt);
    }

    /** @throws DateMalformedStringException */
    public function markUsed(): bool
    {
        if (!$this->isActive()) {
            return false;
        }
        $this->usedAt = UtcClock::now();
        return true;
    }

    /** @throws DateMalformedStringException */
    public function revoke(): bool
    {
        if ($this->revokedAt !== null) {
            return false;
        }
        $this->revokedAt = UtcClock::now();
        return true;
    }

    /** @throws DateMalformedStringException */
    public function getState(): {Entity}State
    {
        return match (true) {
            $this->revokedAt !== null       => {Entity}State::REVOKED,
            $this->usedAt !== null          => {Entity}State::USED,
            $this->expiresAt <= UtcClock::now() => {Entity}State::EXPIRED,
            default                         => {Entity}State::ACTIVE,
        };
    }

    /** @throws DateMalformedStringException */
    public function isActive(): bool
    {
        return $this->getState() === {Entity}State::ACTIVE;
    }
}
```

---

## Пример: сущность с soft delete и `updatedAt`

```php
#[ORM\Entity]
#[ORM\Table(name: '{table_name}')]
class {Entity}
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private(set) string $email;

    #[ORM\Column(type: Types::INTEGER, enumType: {Entity}Role::class)]
    private(set) {Entity}Role $role;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private(set) ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private(set) ?DateTimeImmutable $deletedAt = null;

    // ...

    public function edit(string $email, {Entity}Role $role): void
    {
        $this->assertNotDeleted();
        $this->email = $email;
        $this->role = $role;
        $this->touch();
    }

    private function touch(): void
    {
        $this->updatedAt = UtcClock::now();
    }

    private function assertNotDeleted(): void
    {
        if ($this->deletedAt !== null) {
            throw new DomainExceptionModule(
                module: '{module}',
                message: 'error.{entity}_is_deleted',
                code: 1
            );
        }
    }
}
```

---

## Enum-поля

**Расположение:** `src/Modules/{Module}/Entity/{Entity}/Fields/Enums/{EnumName}.php`

### Колонка в БД → `int`-backed + `getLabel()`

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Entity\{Entity}\Fields\Enums;

enum {EnumName}: int
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

### Только доменная логика (не в БД) → `string`-backed, без `getLabel()`

```php
enum {EnumName}: string
{
    case ACTIVE  = 'active';
    case EXPIRED = 'expired';
    case USED    = 'used';
    case REVOKED = 'revoked';
}
```