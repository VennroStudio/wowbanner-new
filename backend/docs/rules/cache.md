# Cache

Кеш используется в Fetcher'ах, где чтение часто повторяется и результат можно безопасно переиспользовать.

**Расположение:**
- кеш в Fetcher: `src/Modules/{Module}/Query/{Entity}/{Action}/`
- invalidator: `src/Modules/{Module}/Service/{Module}QueryCacheInvalidator.php`

---

## Правило

Новый кеш Fetcher'ов строится через `App\Components\Fetcher`.

- `FetcherCache` читает, пишет и инвалидирует
- `FetcherCacheKey::tag()` строит tag данных
- `FetcherCacheKey::key()` строит key конкретной ReadModel
- для нового Fetcher-cache Handler вызывает `QueryCacheInvalidator`
- Handler не знает, какие DTO были закешированы под tag

---

## Fetcher

```php
use App\Components\Fetcher\FetcherCache;
use App\Components\Fetcher\FetcherCacheKey;

private const int CACHE_TTL = 900;
public const string CACHE_TAG = '{entity}.by_id';

public function __construct(
    private Connection $connection,
    private FetcherCache $fetcherCache,
) {}
```

```php
$tag = FetcherCacheKey::tag(self::CACHE_TAG, [$query->id]);
$key = FetcherCacheKey::key($tag, $modelClass);

/** @var T|null $cached */
$cached = $this->fetcherCache->get($key);

if ($cached !== null) {
    return $cached;
}

$result = $modelClass::fromRow($row);
$this->fetcherCache->set($key, $result, self::CACHE_TTL, [$tag]);

return $result;
```

Tag описывает данные:

```php
material.by_id.10
```

Key описывает конкретную форму ответа:

```php
material.by_id.10.MaterialDetails
material.by_id.10.MaterialIdName
```

---

## Invalidator

Invalidator удаляет tag, а не конкретный key.
Все keys, записанные под этим tag, удаляются автоматически.

```php
use App\Components\Fetcher\FetcherCache;
use App\Components\Fetcher\FetcherCacheKey;
use App\Modules\{Module}\Query\{Entity}\GetById\{Entity}GetByIdFetcher;

final readonly class {Module}QueryCacheInvalidator
{
    public function __construct(
        private FetcherCache $fetcherCache,
    ) {}

    public function invalidateById(int $id): void
    {
        $this->fetcherCache->invalidateTag(
            FetcherCacheKey::tag({Entity}GetByIdFetcher::CACHE_TAG, [$id])
        );
    }
}
```

Если write-сценарий затрагивает связанные данные, invalidator удаляет несколько tag'ов.

```php
public function invalidateContext(int $parentId, int $childId): void
{
    $parts = [$parentId, $childId];

    $this->fetcherCache->invalidateTag(
        FetcherCacheKey::tag({Entity}PriceFetcher::CACHE_TAG, $parts)
    );
    $this->fetcherCache->invalidateTag(
        FetcherCacheKey::tag({Entity}ProcessingFetcher::CACHE_TAG, $parts)
    );
}
```

---

## Legacy

Прямое удаление через `Cacher::delete()` допустимо только для старого кеша с одним прямым ключом.
Для нового Fetcher-cache используется только tag-инвалидация.
