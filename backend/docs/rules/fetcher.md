# Fetcher / Query — Запрос на чтение

Fetcher — **единственный способ чтения данных**. Чтение через Repository запрещено.

**Расположение:** `src/Modules/{Module}/Query/{Entity}/{Action}/`
- `{Entity}{Action}Query.php`
- `{Entity}{Action}Fetcher.php`

---

## Query

- Простой запрос (по ID или полю) — `final readonly class`
- Список — `final class` (не readonly: свойства мутабельны при денормализации), с пагинацией и `getOffset()`
- Валидация списка — через `#[Assert\...]`

### Простой запрос

```php
final readonly class {Entity}GetByIdQuery
{
    public function __construct(
        public int $id,
    ) {}
}

// Несколько параметров:
final readonly class {Entity}FindBy{Field}Query
{
    public function __construct(
        public string $hash,
        public {Entity}Type $type,
    ) {}
}
```

### Запрос списка с пагинацией

```php
final class {Entity}FindAllQuery
{
    #[Assert\Positive]
    public int $page = 1;

    #[Assert\Positive]
    #[Assert\LessThanOrEqual(100)]
    public int $perPage = 20;

    public ?string $search = null;

    #[Assert\Date]
    public ?string $dateFrom = null;

    #[Assert\Date]
    public ?string $dateTo = null;

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
```

---

## Fetcher

- `final readonly class`
- Зависимость — `Connection` (DBAL, не EntityManager)
- Константа `TABLE` — имя таблицы
- Имена методов по семантике: `fetch()`, `fetchAny()`, `fetchNotDeleted()`
- Одна запись: `?ReadModel` (nullable) или `DomainExceptionModule` если запись обязательна
- Список: `ModelCountItemsResult<ReadModel>` — COUNT через клонирование QueryBuilder
- Enum в условии — передавать как `$query->type->value`
- Кеш — через `Cacher`; инвалидация в соответствующем Handler'е

### Одна запись (nullable)

```php
final readonly class {Entity}FindBy{Field}Fetcher
{
    private const string TABLE = '{table_name}';

    public function __construct(
        private Connection $connection,
    ) {}

    /** @throws Exception */
    public function fetchAny({Entity}FindBy{Field}Query $query): ?{Entity}By{Field}
    {
        $row = $this->connection->createQueryBuilder()
            ->select('id', 'field_one', 'field_two')
            ->from(self::TABLE)
            ->where('field = :field')
            ->setParameter('field', $query->field)
            ->setMaxResults(1)
            ->fetchAssociative();

        return $row !== false ? {Entity}By{Field}::fromRow($row) : null;
    }

    /** @throws Exception */
    public function fetchNotDeleted({Entity}FindBy{Field}Query $query): ?{Entity}By{Field}
    {
        $row = $this->connection->createQueryBuilder()
            ->select('id', 'field_one', 'field_two')
            ->from(self::TABLE)
            ->where('field = :field')
            ->andWhere('deleted_at IS NULL')
            ->setParameter('field', $query->field)
            ->setMaxResults(1)
            ->fetchAssociative();

        return $row !== false ? {Entity}By{Field}::fromRow($row) : null;
    }
}
```

### Одна запись (обязательная, с кешем)

```php
final readonly class {Entity}GetByIdFetcher
{
    private const string TABLE = '{table_name}';
    private const int CACHE_TTL = 900;

    public function __construct(
        private Connection $connection,
        private Cacher $cacher,
    ) {}

    /** @throws Exception */
    public function fetch({Entity}GetByIdQuery $query): {Entity}ById
    {
        $key = '{entity}_' . $query->id;

        /** @var {Entity}ById|null $cached */
        $cached = $this->cacher->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $row = $this->connection->createQueryBuilder()
            ->select('id', 'field_one', 'field_two', 'deleted_at')
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

        $result = {Entity}ById::fromRow($row);
        $this->cacher->set($key, $result, self::CACHE_TTL);

        return $result;
    }
}
```

### Список с пагинацией

```php
final readonly class {Entity}FindAllFetcher
{
    private const string TABLE = '{table_name}';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return ModelCountItemsResult<{Entity}FindAll>
     * @throws Exception
     */
    public function fetch({Entity}FindAllQuery $query): ModelCountItemsResult
    {
        $qb = $this->connection->createQueryBuilder()
            ->from(self::TABLE)
            ->andWhere('deleted_at IS NULL');

        if ($query->search !== null && $query->search !== '') {
            $qb->andWhere('name ILIKE :search')
                ->setParameter('search', '%' . $query->search . '%');
        }

        if ($query->dateFrom !== null) {
            $qb->andWhere('created_at >= :dateFrom')
                ->setParameter('dateFrom', $query->dateFrom . ' 00:00:00');
        }

        if ($query->dateTo !== null) {
            $qb->andWhere('created_at <= :dateTo')
                ->setParameter('dateTo', $query->dateTo . ' 23:59:59');
        }

        $total = (int) (clone $qb)->select('COUNT(id)')->executeQuery()->fetchOne();

        $rows = $qb
            ->select('id', 'field_one', 'field_two', 'created_at')
            ->orderBy('created_at', 'DESC')
            ->setFirstResult($query->getOffset())
            ->setMaxResults($query->perPage)
            ->executeQuery()
            ->fetchAllAssociative();

        return new ModelCountItemsResult(
            items: {Entity}FindAll::fromRows($rows),
            count: $total,
        );
    }
}
```