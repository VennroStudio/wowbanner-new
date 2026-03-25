# ReadModel — DTO для чтения

Проекция данных из БД для Fetcher'а. Один Fetcher — один ReadModel.

**Расположение:**
- `src/Modules/{Module}/ReadModel/{Entity}/{Entity}By{Field}.php` (или `{Entity}FindAll.php` и т.д.)
- `src/Modules/{Module}/ReadModel/{Entity}/Interface/{Entity}ModelInterface.php`

---

## Правила

- `final readonly class`, реализует `{Entity}ModelInterface`
- Интерфейс: минимум `getId(): int` и `toArray(): array`; расширяется геттерами если нужен доступ к полям в коде
- `fromRow(array $row): self` — маппинг строки БД; PHPDoc с array shape для `$row`
- `FromRowsTrait` — подключать в ReadModel'ах для списков; даёт `fromRows()` автоматически
- Enum в `toArray()` — `['id' => $enum->value, 'label' => $enum->getLabel()]`
- Ключи `toArray()` для API — snake_case; для внутреннего использования допустим camelCase
- `#[Override]` на методах интерфейса

---

## Интерфейс

```php
// Базовый:
interface {Entity}ModelInterface
{
    public function getId(): int;
    public function toArray(): array;
}

// Расширенный (если нужен доступ к полям в коде, не только в JSON):
interface {Entity}ModelInterface
{
    public function getId(): int;
    public function getOwnerId(): int;
    public function getExpiresAt(): string;
    public function getRevokedAt(): ?string;
    public function toArray(): array;
}
```

---

## Пример — проекция по ID (с enum, для API)

`FromRowsTrait` подключается для единообразия даже если запись одна.

```php
final readonly class {Entity}ById implements {Entity}ModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public {Entity}Role $role,
        public {Entity}Status $status,
        public string $name,
        public string $email,
        public ?string $avatar,
        public string $createdAt,
        public ?string $updatedAt,
        public ?string $deletedAt,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     role: int,
     *     status: int,
     *     name: string,
     *     email: string,
     *     avatar: string|null,
     *     created_at: string,
     *     updated_at: string|null,
     *     deleted_at: string|null
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: $row['id'],
            role: {Entity}Role::from($row['role']),
            status: {Entity}Status::from($row['status']),
            name: $row['name'],
            email: $row['email'],
            avatar: $row['avatar'],
            createdAt: $row['created_at'],
            updatedAt: $row['updated_at'],
            deletedAt: $row['deleted_at'],
        );
    }

    #[Override]
    public function getId(): int { return $this->id; }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'role'       => ['id' => $this->role->value, 'label' => $this->role->getLabel()],
            'status'     => ['id' => $this->status->value, 'label' => $this->status->getLabel()],
            'name'       => $this->name,
            'email'      => $this->email,
            'avatar'     => $this->avatar,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'deleted_at' => $this->deletedAt,
        ];
    }
}
```

---

## Пример — элемент списка (с FromRowsTrait)

`FromRowsTrait` обязателен — Fetcher вызывает `{Entity}FindAll::fromRows($rows)`.

```php
final readonly class {Entity}FindAll implements {Entity}ModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public {Entity}Role $role,
        public string $name,
        public string $email,
        public string $createdAt,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: $row['id'],
            role: {Entity}Role::from($row['role']),
            name: $row['name'],
            email: $row['email'],
            createdAt: $row['created_at'],
        );
    }

    #[Override]
    public function getId(): int { return $this->id; }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'role'       => ['id' => $this->role->value, 'label' => $this->role->getLabel()],
            'name'       => $this->name,
            'email'      => $this->email,
            'created_at' => $this->createdAt,
        ];
    }
}
```

---

## Пример — внутренняя проекция (без FromRowsTrait, расширенный интерфейс)

Используется только для одной записи во внутренней логике. `FromRowsTrait` не нужен. `toArray()` допустим в camelCase.

```php
final readonly class {Entity}By{Field} implements {Entity}ModelInterface
{
    public function __construct(
        public int $id,
        public int $ownerId,
        public string $expiresAt,
        public ?string $revokedAt,
        public ?string $usedAt,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: $row['id'],
            ownerId: $row['owner_id'],
            expiresAt: $row['expires_at'],
            revokedAt: $row['revoked_at'],
            usedAt: $row['used_at'],
        );
    }

    #[Override]
    public function getId(): int { return $this->id; }

    #[Override]
    public function getOwnerId(): int { return $this->ownerId; }

    #[Override]
    public function getExpiresAt(): string { return $this->expiresAt; }

    #[Override]
    public function getRevokedAt(): ?string { return $this->revokedAt; }

    #[Override]
    public function getUsedAt(): ?string { return $this->usedAt; }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'ownerId'   => $this->ownerId,
            'expiresAt' => $this->expiresAt,
            'revokedAt' => $this->revokedAt,
            'usedAt'    => $this->usedAt,
        ];
    }
}
```