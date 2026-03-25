Изучи правила проекта в docs/agent-rules.md.

## Задача
Создай модуль `Project` в `src/Modules/Project`.

## Описание
Пользователь регистрируется/логинится через существующий модуль User.
После входа он может создавать проекты. Каждый проект получает уникальный
`token` — это и есть DSN. SDK `vennro/checkbug` шлёт данные на:
POST /{token}/logs
POST /{token}/errors
Никакого отдельного API ключа нет — токен и есть защита.

## Поля сущности `Project`

| Поле      | Тип               | Обязательное | Описание                                            |
|-----------|-------------------|--------------|-----------------------------------------------------|
| id        | int (auto)        | да           | Первичный ключ                                      |
| userId    | int               | да           | ID владельца (из Identity, не ORM relation)         |
| name      | string (255)      | да           | Название проекта                                    |
| token     | string (64)       | да           | Уникальный DSN-токен, bin2hex(random_bytes(32))     |
| createdAt | DateTimeImmutable | да           | Дата создания                                       |
| updatedAt | DateTimeImmutable | нет          | Дата обновления                                     |
| deletedAt | DateTimeImmutable | нет          | Soft delete                                         |

## Методы API

### 1. Создание проекта
- Метод: `POST`
- URL: `/v1/projects`
- Защищённый: да (Bearer JWT)
- Поля: `name` (2–255)
- `userId` берётся из `Identity`, НЕ из тела запроса
- `token` генерируется в Handler: `bin2hex(random_bytes(32))`
- Уникальность `token` проверять через `ProjectFindByTokenFetcher`.
  Если коллизия — генерировать заново (цикл do/while)
- DSN строится через `DsnGenerator::generate($token)`
- Ответ: `201 { "data": { "id": 1, "name": "My App", "token": "abc...", "dsn": "https://tracker.example.com/abc...", "createdAt": "..." } }`

### 2. Список проектов текущего пользователя
- Метод: `GET`
- URL: `/v1/projects`
- Защищённый: да (Bearer JWT)
- Фильтрация всегда по `userId` из `Identity`
- Пагинация: `page`, `perPage`
- Ответ: `200 { "data": { "count": N, "items": [ { "id": 1, "name": "...", "token": "...", "dsn": "...", "createdAt": "..." } ] } }`

### 3. Получение проекта по ID
- Метод: `GET`
- URL: `/v1/projects/{id}`
- Защищённый: да (Bearer JWT)
- Проверять что `userId` проекта совпадает с `Identity->id`,
  иначе `AccessDeniedException`
- Ответ: `200 { "data": { "id": 1, "name": "...", "token": "...", "dsn": "...", "createdAt": "...", "updatedAt": null } }`

### 4. Удаление проекта (soft delete)
- Метод: `DELETE`
- URL: `/v1/projects/{id}`
- Защищённый: да (Bearer JWT)
- Проверять что `userId` проекта совпадает с `Identity->id`,
  иначе `AccessDeniedException`
- Ответ: `200 { "data": { "success": 1 } }`

## Бизнес-правила
- `token` генерируется только автоматически, никогда не принимается от клиента
- `token` уникален глобально
- Удалённый проект: `deletedAt IS NOT NULL` — модуль Tracker будет
  проверять это при приёме данных от SDK и возвращать 404
- Пользователь видит и управляет только своими проектами

## Компонент DsnGenerator
Создай `src/Components/Dsn/DsnGenerator.php`:

final readonly class DsnGenerator
{
public function __construct(private string $appUrl) {}

    public function generate(string $token): string
    {
        return rtrim($this->appUrl, '/') . '/' . $token;
    }
}

Зарегистрировать в `config/container.php`:
DsnGenerator::class => fn() => new DsnGenerator($_ENV['APP_URL']),

## Авторизация
Используется существующий `Authenticate` middleware и `Identity`.
В Action получать identity так:
$identity = Authenticate::getIdentity($request);

## Что создать
Следуя docs/agent-rules.md, создай все компоненты в правильном порядке:
1. Component — `DsnGenerator` (`src/Components/Dsn/DsnGenerator.php`)
2. Entity — `Project`
3. Repository — `ProjectRepository` + `DoctrineProjectRepository`
4. Command/Handler — `CreateProjectCommand/Handler`, `DeleteProjectCommand/Handler`
5. Query/Fetcher:
    - `ProjectGetByIdQuery/Fetcher` — для детального просмотра и проверки владельца
    - `ProjectFindAllQuery/Fetcher` — список проектов пользователя
    - `ProjectFindByTokenQuery/Fetcher` — поиск по токену (используется в Handler
      для проверки уникальности и в Tracker модуле для валидации DSN)
6. ReadModel:
    - `ProjectViewInterface` — `getId()`, `toArray()`
    - `ProjectById` — полная проекция включая `dsn`
    - `ProjectListItem` — проекция для списка включая `dsn`
    - DSN в ReadModel строить через `DsnGenerator` передавая его в Fetcher
7. Action:
    - `CreateProjectAction` — POST /v1/projects
    - `GetProjectsAction` — GET /v1/projects
    - `GetProjectAction` — GET /v1/projects/{id}
    - `DeleteProjectAction` — DELETE /v1/projects/{id}
8. Роуты — добавить в `config/app.php` под middleware `Authenticate`
9. `.env.example` — добавить `APP_URL=https://tracker.example.com`
10. Frontend docs — `docs/frontend/Project.md`

Миграции НЕ создавать.