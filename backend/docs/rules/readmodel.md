# ReadModel — DTO для чтения

ReadModel — проекция данных из БД для Fetcher'а.

**Расположение:**

- `src/Modules/{Module}/ReadModel/{Entity}/{Entity}By{Field}.php`
- `src/Modules/{Module}/ReadModel/{Entity}/{Entity}FindAll.php`
- `src/Modules/{Module}/ReadModel/{Entity}/{Entity}GetBySelect.php`
- `src/Modules/{Module}/ReadModel/{Entity}/Interface/{Entity}ModelInterface.php`

---

## Состав ReadModel

ReadModel собирается только из тех блоков, которые нужны конкретной проекции.

- Интерфейс модели
- `final readonly class`
- Публичные readonly-свойства через конструктор
- `fromRow()`
- `FromRowsTrait`
- Геттеры интерфейса
- `toArray()`

---

## Интерфейс

Интерфейс задает общий контракт для ReadModel одной сущности.
Минимально нужны `getId()` и `toArray()`.

Если ReadModel используется во внутренней логике, интерфейс можно расширить геттерами нужных полей.

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\ReadModel\{Entity}\Interface;

interface {Entity}ModelInterface
{
    public function getId(): int;

    public function toArray(): array;
}
```

---

## Класс ReadModel

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\ReadModel\{Entity};

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\{Module}\Entity\{Entity}\Fields\Enums\{Entity}Status;
use App\Modules\{Module}\ReadModel\{Entity}\Interface\{Entity}ModelInterface;
use Override;

final readonly class {Entity}ById implements {Entity}ModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public string $name,
        public {Entity}Status $status,
        public string $createdAt,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     name: string,
     *     status: int,
     *     created_at: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            name: $row['name'],
            status: {Entity}Status::from((int) $row['status']),
            createdAt: $row['created_at'],
        );
    }

    #[Override]
    public function getId(): int
    {
        return $this->id;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => [
                'id' => $this->status->value,
                'label' => $this->status->getLabel(),
            ],
            'created_at' => $this->createdAt,
        ];
    }
}
```

---

## fromRow()

`fromRow()` преобразует строку БД в ReadModel.

Для `$row` указывается PHPDoc array shape.
Поля БД приходят в `snake_case`, поля объекта обычно в `camelCase`.

```php
/**
 * @param array{
 *     id: int,
 *     owner_id: int,
 *     created_at: string
 * } $row
 */
public static function fromRow(array $row): self
{
    return new self(
        id: (int) $row['id'],
        ownerId: (int) $row['owner_id'],
        createdAt: $row['created_at'],
    );
}
```

---

## toArray()

`toArray()` готовит данные для API.

Ключи ответа всегда пишутся в `snake_case`.
Enum-поля отдаются как объект `{ id, label }`.

```php
#[Override]
public function toArray(): array
{
    return [
        'id' => $this->id,
        'is_cut' => $this->isCut,
        'status_type' => [
            'id' => $this->statusType->value,
            'label' => $this->statusType->getLabel(),
        ],
        'pricing_type' => [
            'id' => $this->pricingType->value,
            'label' => $this->pricingType->getLabel(),
        ],
        'created_at' => $this->createdAt,
    ];
}
```

---

## Внутренняя ReadModel

Если ReadModel используется не для API-ответа, а внутри backend-логики, интерфейс может иметь дополнительные геттеры.

```php
interface {Entity}TokenModelInterface
{
    public function getId(): int;

    public function getUserId(): int;

    public function getExpiresAt(): string;

    public function toArray(): array;
}
```

---

## Компоненты ReadModel

Общие компоненты лежат в `src/Components/ReadModel/`.
Они нужны, чтобы не дублировать одинаковую логику в каждом модуле.

### FromRowsTrait

Используется в ReadModel, когда Fetcher вызывает `{Entity}ReadModel::fromRows($rows)`.
В проекте trait подключается почти во всех ReadModel для единообразия.
Если ReadModel точно используется только для одной записи и `fromRows()` не нужен, trait можно не подключать.

```php
use App\Components\ReadModel\FromRowsTrait;

final readonly class {Entity}FindAll implements {Entity}ModelInterface
{
    use FromRowsTrait;
}
```

### ReadModelArray

Используется в Action, когда нужно преобразовать список ReadModel в массивы через `toArray()`.

```php
use App\Components\ReadModel\ReadModelArray;

return new JsonDataResponse(
    ReadModelArray::fromItems(
        $this->fetcher->fetch(new {Entity}GetBySelectQuery())
    )
);
```

### ModelCountItemsResult

Используется в Fetcher'ах списков с пагинацией.
Хранит `items` и общее `count`.

```php
use App\Components\ReadModel\ModelCountItemsResult;

return new ModelCountItemsResult(
    items: {Entity}FindAll::fromRows($rows),
    count: $total,
);
```
