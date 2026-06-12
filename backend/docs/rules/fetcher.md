# Fetcher / Query — Запрос на чтение

**Расположение:** `src/Modules/{Module}/Query/{Entity}/{Action}/`

- `{Entity}{Action}Query.php`
- `{Entity}{Action}Fetcher.php`

Fetcher читает данные через DBAL `Connection`.
Repository для чтения не используется.

---

## Get и Find

### Get

`Get` используется, когда запись обязательна.

- нашел запись — возвращает ReadModel;
- не нашел запись — бросает `DomainExceptionModule`;
- метод обычно называется `fetch()`;
- пример: `GetById`.

### Find

`Find` используется, когда отсутствие результата является нормальной ситуацией.

- нашел запись — возвращает ReadModel;
- не нашел запись — возвращает `null`;
- метод может называться `fetch()`;
- если нужны варианты с удаленными записями, используются `fetchAny()` и `fetchNotDeleted()`;
- пример: `FindByEmail`.

Для списков `Find` возвращает список или `ModelCountItemsResult`.
Пустой список — нормальный результат.

---

## Состав

Fetcher / Query собирается только из тех блоков, которые нужны конкретному запросу.

- Query
- Fetcher
- Фильтры
- Пагинация
- JOIN для фильтрации
- Кеширование

Кеширование описывается отдельно в [Cache](cache.md).

---

## Query

### Простой Query

Используется для запроса по ID, email, hash или другому конкретному полю.

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Query\{Entity}\GetById;

final readonly class {Entity}GetByIdQuery
{
    public function __construct(
        public int $id,
    ) {}
}
```

### Query списка

Списки не `readonly`, потому что свойства заполняются через денормализацию.

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Query\{Entity}\FindAll;

use Symfony\Component\Validator\Constraints as Assert;

final class {Entity}FindAllQuery
{
    #[Assert\Positive]
    public int $page = 1;

    #[Assert\Positive]
    #[Assert\LessThanOrEqual(100)]
    public int $perPage = 20;

    public ?string $search = null;

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
```

---

## Fetcher

### Заголовок класса

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Query\{Entity}\{Action};

use Doctrine\DBAL\Connection;

final readonly class {Entity}{Action}Fetcher
{
    private const string TABLE = '{table_name}';

    public function __construct(
        private Connection $connection,
    ) {}
}
```

---

## Get: обязательная запись

```php
use App\Components\Exception\DomainExceptionModule;
use App\Modules\{Module}\ReadModel\{Entity}\{Entity}ById;
use Doctrine\DBAL\Exception;

/**
 * @throws Exception
 */
public function fetch({Entity}GetByIdQuery $query): {Entity}ById
{
    $row = $this->connection->createQueryBuilder()
        ->select('id', 'name', 'created_at')
        ->from(self::TABLE)
        ->where('id = :id')
        ->andWhere('deleted_at IS NULL')
        ->setParameter('id', $query->id)
        ->setMaxResults(1)
        ->executeQuery()
        ->fetchAssociative();

    if ($row === false) {
        throw new DomainExceptionModule(
            module: '{module}',
            message: 'error.{entity}_not_found',
            code: 1
        );
    }

    return {Entity}ById::fromRow($row);
}
```

---

## Find: nullable-запись

```php
use App\Modules\{Module}\ReadModel\{Entity}\{Entity}ByEmail;
use Doctrine\DBAL\Exception;

/**
 * @throws Exception
 */
public function fetch({Entity}FindByEmailQuery $query): ?{Entity}ByEmail
{
    $row = $this->connection->createQueryBuilder()
        ->select('id', 'email', 'name', 'deleted_at')
        ->from(self::TABLE)
        ->where('email = :email')
        ->setParameter('email', mb_strtolower($query->email))
        ->setMaxResults(1)
        ->executeQuery()
        ->fetchAssociative();

    return $row !== false ? {Entity}ByEmail::fromRow($row) : null;
}
```

Если для одного запроса нужны разные варианты чтения, используются отдельные методы:

```php
public function fetchAny({Entity}FindByEmailQuery $query): ?{Entity}ByEmail
{
    // query without deleted_at condition
}

public function fetchNotDeleted({Entity}FindByEmailQuery $query): ?{Entity}ByEmail
{
    // query with deleted_at IS NULL
}
```

---

## FindAll: список с пагинацией

```php
use App\Components\ReadModel\ModelCountItemsResult;
use App\Modules\{Module}\ReadModel\{Entity}\{Entity}FindAll;
use Doctrine\DBAL\Exception;

/**
 * @return ModelCountItemsResult<{Entity}FindAll>
 * @throws Exception
 */
public function fetch({Entity}FindAllQuery $query): ModelCountItemsResult
{
    $qb = $this->connection->createQueryBuilder()
        ->from(self::TABLE);

    if ($query->search !== null && $query->search !== '') {
        $qb->andWhere('LOWER(name) LIKE LOWER(:search)')
            ->setParameter('search', '%' . $query->search . '%');
    }

    $total = (int) (clone $qb)
        ->select('COUNT(id)')
        ->executeQuery()
        ->fetchOne();

    $rows = $qb->select('id', 'name', 'created_at')
        ->orderBy('id', 'DESC')
        ->setFirstResult($query->getOffset())
        ->setMaxResults($query->perPage)
        ->executeQuery()
        ->fetchAllAssociative();

    return new ModelCountItemsResult(
        items: {Entity}FindAll::fromRows($rows),
        count: $total,
    );
}
```

---

## GetBySelect

Используется для коротких списков в селектах.
Это список, а не `Get` одной обязательной записи.
Пустой список — нормальный результат.

```php
/**
 * @return list<{Entity}GetBySelect>
 * @throws Exception
 */
public function fetch({Entity}GetBySelectQuery $query): array
{
    $rows = $this->connection->createQueryBuilder()
        ->select('id', 'name')
        ->from(self::TABLE)
        ->orderBy('name', 'ASC')
        ->executeQuery()
        ->fetchAllAssociative();

    return {Entity}GetBySelect::fromRows($rows);
}
```

---

## Exists

Используется, когда нужно проверить существование записи.

```php
public function exists({Entity}EmailExistsQuery $query): bool
{
    $qb = $this->connection->createQueryBuilder()
        ->select('1')
        ->from(self::TABLE)
        ->where('email = :email')
        ->setParameter('email', $query->email)
        ->setMaxResults(1);

    if ($query->excludeEntityId !== null) {
        $qb->andWhere('id != :excludeEntityId')
            ->setParameter('excludeEntityId', $query->excludeEntityId);
    }

    return $qb->executeQuery()->fetchOne() !== false;
}
```

---

## FindByIds

Для `IN (:ids)` используется `ArrayParameterType::INTEGER`.
Пустой список сразу возвращает `[]`.

```php
use Doctrine\DBAL\ArrayParameterType;

/**
 * @return list<{Entity}ByParent>
 */
public function fetch({Entity}FindByIdsQuery $query): array
{
    if ($query->ids === []) {
        return [];
    }

    $rows = $this->connection->createQueryBuilder()
        ->select('id', 'parent_id', 'name')
        ->from(self::TABLE)
        ->where('parent_id IN (:ids)')
        ->setParameter('ids', $query->ids, ArrayParameterType::INTEGER)
        ->executeQuery()
        ->fetchAllAssociative();

    return {Entity}ByParent::fromRows($rows);
}
```

---

## JOIN для фильтрации

Если основной список фильтруется по связанным таблицам, связанный Fetcher может дать метод `joinForFilter()`.

```php
use Doctrine\DBAL\Query\QueryBuilder;

private const string TABLE = 'order_items';
public const string ALIAS = 'oi';

public function joinForFilter(QueryBuilder $qb, string $alias): void
{
    $qb->leftJoin($alias, self::TABLE, self::ALIAS, self::ALIAS . '.order_id = ' . $alias . '.id');
}
```

При JOIN один-ко-многим:

- `COUNT(DISTINCT {main_alias}.id)`
- `groupBy('{main_alias}.id')`

### Использование в основном Fetcher

```php
private const string TABLE = 'orders';

public function __construct(
    private Connection $connection,
    private ClientFindAllFetcher $clientFetcher,
    private OrderItemFindByOrderIdFetcher $orderItemFetcher,
    private OrderServiceFindByOrderIdFetcher $orderServiceFetcher,
) {}

public function fetch(OrderFindAllQuery $query): ModelCountItemsResult
{
    $qb = $this->connection->createQueryBuilder()
        ->from(self::TABLE, 'o');

    $this->clientFetcher->joinForFilter($qb, 'o');
    $this->orderItemFetcher->joinForFilter($qb, 'o');
    $this->orderServiceFetcher->joinForFilter($qb, 'o');

    if ($query->search !== null && $query->search !== '') {
        $clientAlias = ClientFindAllFetcher::ALIAS;

        $qb->andWhere(
            $qb->expr()->or(
                "LOWER({$clientAlias}.old_full_name) LIKE LOWER(:search)",
                "LOWER({$clientAlias}.last_name) LIKE LOWER(:search)",
                "LOWER({$clientAlias}.first_name) LIKE LOWER(:search)"
            )
        )->setParameter('search', '%' . $query->search . '%');
    }

    if ($query->materialId !== null) {
        $qb->andWhere(OrderItemFindByOrderIdFetcher::ALIAS . '.material_id = :materialId')
            ->setParameter('materialId', $query->materialId);
    }

    if ($query->serviceType !== null) {
        $qb->andWhere(OrderServiceFindByOrderIdFetcher::ALIAS . '.service_type = :serviceType')
            ->setParameter('serviceType', $query->serviceType);
    }

    $countQb = clone $qb;
    $total = (int) $countQb->select('COUNT(DISTINCT o.id)')->executeQuery()->fetchOne();

    $qb->groupBy('o.id');

    $rows = $qb->select('o.id', 'o.client_id', 'o.status_type', 'o.created_at')
        ->orderBy('o.id', 'DESC')
        ->setFirstResult($query->getOffset())
        ->setMaxResults($query->perPage)
        ->executeQuery()
        ->fetchAllAssociative();

    return new ModelCountItemsResult(
        items: OrderFindAll::fromRows($rows),
        count: $total,
    );
}
```

---

## Поиск

Для регистронезависимого поиска:

```php
$qb->andWhere('LOWER(name) LIKE LOWER(:search)')
    ->setParameter('search', '%' . $query->search . '%');
```

`ILIKE` не использовать.

Enum в условии передается через `->value`.
