Изучи правила проекта в docs/agent-rules.md.

## Задача
Создай сущности `ErrorGroup` и `ErrorEvent` в модуле `src/Modules/Tracker`.
Модуль Tracker уже частично создан (LogGroup, LogEvent) — добавляй рядом,
не трогая существующий код.

## Описание
SDK шлёт ошибку на POST /{token}/errors при поимке Throwable.
Ошибки группируются по уникальной паре (projectId + class + message).
Логика полностью симметрична модулю Log, но данные богаче — есть stacktrace.

## Сущность `ErrorGroup`

| Поле        | Тип               | Обязательное | Описание                                        |
|-------------|-------------------|--------------|-------------------------------------------------|
| id          | int (auto)        | да           | Первичный ключ                                  |
| projectId   | int               | да           | ID проекта (не ORM relation)                    |
| class       | string (512)      | да           | Класс исключения — ключ группировки             |
| message     | string (text)     | да           | Сообщение исключения — ключ группировки         |
| file        | string (512)      | да           | Файл где брошено (из первого события)           |
| line        | int               | да           | Строка где брошено (из первого события)         |
| firstSeenAt | DateTimeImmutable | да           | Время первого события                           |
| lastSeenAt  | DateTimeImmutable | да           | Время последнего события (обновляется)          |

## Сущность `ErrorEvent`

| Поле               | Тип               | Обязательное | Описание                                    |
|--------------------|-------------------|--------------|---------------------------------------------|
| id                 | int (auto)        | да           | Первичный ключ                              |
| groupId            | int               | да           | ID группы (не ORM relation)                 |
| projectId          | int               | да           | ID проекта (не ORM relation)                |
| file               | string (512)      | да           | Файл где брошено                            |
| line               | int               | да           | Строка где брошено                          |
| code               | int               | да           | Код исключения                              |
| stacktrace         | json              | нет          | Массив трейса                               |
| stacktraceAsString | text              | нет          | Трейс строкой                               |
| previous           | json              | нет          | Цепочка предыдущих исключений               |
| context            | json              | нет          | Дополнительный контекст                     |
| ip                 | string (45)       | нет          | IP клиента                                  |
| url                | text              | нет          | URL запроса                                 |
| method             | string (16)       | нет          | HTTP метод                                  |
| headers            | json              | нет          | Заголовки                                   |
| queryParams        | json              | нет          | Query параметры                             |
| bodyParams         | json              | нет          | Body параметры                              |
| cookies            | json              | нет          | Cookies                                     |
| session            | json              | нет          | Session                                     |
| files              | json              | нет          | Files                                       |
| createdAt          | DateTimeImmutable | да           | Время события (из поля `time` в payload)    |

## Payload от SDK (POST /{token}/errors)

{
"time":               1712345678000,
"file":               "/var/www/src/Service/OrderService.php",
"line":               42,
"class":              "RuntimeException",
"message":            "Payment gateway timeout",
"code":               0,
"stacktrace":         [...],
"stacktraceAsString": "#0 ...",
"previous":           [],
"context":            { "order_id": 99 },
"ip":                 "1.2.3.4",
"url":                "https://app.example.com/checkout",
"method":             "POST",
"headers":            { ... },
"queryParams":        null,
"bodyParams":         { "amount": 100, "password": "***" },
"cookies":            null,
"session":            null,
"files":              null
}

## Методы API

### SDK (публичный, без авторизации)

#### POST /{token}/errors
- Найти Project по token через `ProjectFindByTokenFetcher`
- Если не найден или `deletedAt IS NOT NULL` — вернуть 404
- Найти ErrorGroup по `projectId + class + message` через `ErrorGroupFindByKeyFetcher`
- Если не найдена — создать через `CreateErrorGroupHandler`
  (file и line берутся из payload — это данные первого события)
- Создать ErrorEvent через `CreateErrorEventHandler`
- Обновить `lastSeenAt` у ErrorGroup через `UpdateErrorGroupHandler`
- Ответ: 201 { "data": { "ok": true } }

### Авторизованные (Bearer JWT, владелец проекта)

#### GET /v1/projects/{projectId}/errors
- Проверить владельца: `ProjectGetByIdFetcher` → если `userId != identity->id` → `AccessDeniedException`
- Список ErrorGroup по projectId с количеством событий
- Счётчики через COUNT из error_events (total, 24ч, 7д, 30д)
- Фильтры: `search` (по message ILIKE), `class` (ILIKE), `dateFrom`, `dateTo` (по lastSeenAt)
- Пагинация: `page`, `perPage`
- Ответ:
  200 {
  "data": {
  "count": N,
  "items": [
  {
  "id": 1,
  "class": "RuntimeException",
  "message": "Payment gateway timeout",
  "file": "/var/www/src/...",
  "line": 42,
  "totalCount": 15,
  "count24h": 3,
  "count7d": 10,
  "count30d": 15,
  "firstSeenAt": "2025-07-24 17:23:00",
  "lastSeenAt": "2025-07-24 17:26:00"
  }
  ]
  }
  }

#### GET /v1/projects/{projectId}/errors/{groupId}
- Проверить владельца проекта
- Детальная группа + счётчики 24ч/7д/30д
- Ответ: 200 { "data": { ...группа с счётчиками... } }

#### GET /v1/projects/{projectId}/errors/{groupId}/events
- Проверить владельца проекта
- Список событий группы, сортировка по createdAt DESC
- Пагинация: `page`, `perPage`
- Ответ:
  200 {
  "data": {
  "count": N,
  "items": [
  {
  "id": 1,
  "file": "/var/www/...",
  "line": 42,
  "code": 0,
  "ip": "1.2.3.4",
  "url": "https://...",
  "method": "POST",
  "createdAt": "2025-07-24 17:26:00"
  }
  ]
  }
  }

#### GET /v1/projects/{projectId}/errors/{groupId}/events/{eventId}
- Проверить владельца проекта
- Детальное событие со всеми полями включая stacktrace, previous, headers и тд
- Ответ: 200 { "data": { ...все поля события... } }

#### DELETE /v1/projects/{projectId}/errors/{groupId}
- Проверить владельца проекта
- Физически удалить все ErrorEvent группы
- Физически удалить ErrorGroup
- Ответ: 200 { "data": { "success": 1 } }

## Бизнес-правила
- Группировка по уникальному ключу: projectId + class + message
- `file` и `line` в ErrorGroup — из ПЕРВОГО события, не обновляются
- `firstSeenAt` устанавливается один раз при создании группы
- `lastSeenAt` обновляется при каждом новом событии
- Счётчики 24ч/7д/30д — всегда COUNT из error_events, никогда не хранить
- Удаление группы — каскадное: сначала все события, потом группа
- Проверка владельца проекта — в каждом авторизованном Action

## Что создать
Следуя docs/agent-rules.md, создай все компоненты:

1. Entity — `ErrorGroup` (`src/Modules/Tracker/Entity/ErrorGroup/ErrorGroup.php`)
    - Методы: `create()`, `updateLastSeenAt(DateTimeImmutable $at)`
2. Entity — `ErrorEvent` (`src/Modules/Tracker/Entity/ErrorEvent/ErrorEvent.php`)
    - Метод: `create()`
3. Repository:
    - `ErrorGroupRepository` + `DoctrineErrorGroupRepository`
    - `ErrorEventRepository` + `DoctrineErrorEventRepository`
4. Command/Handler:
    - `CreateErrorGroupCommand/Handler`
    - `CreateErrorEventCommand/Handler`
    - `UpdateErrorGroupLastSeenCommand/Handler`
    - `DeleteErrorGroupCommand/Handler` — удалить группу + все события
5. Query/Fetcher:
    - `ErrorGroupFindByKeyQuery/Fetcher` — поиск по (projectId + class + message)
    - `ErrorGroupFindAllQuery/Fetcher` — список с COUNT (total, 24h, 7d, 30d)
    - `ErrorGroupGetByIdQuery/Fetcher` — детальная группа с COUNT
    - `ErrorEventFindAllQuery/Fetcher` — список событий группы
    - `ErrorEventGetByIdQuery/Fetcher` — детальное событие
6. ReadModel:
    - `ErrorGroupViewInterface`
    - `ErrorGroupListItem`
    - `ErrorGroupDetail`
    - `ErrorEventViewInterface`
    - `ErrorEventListItem`
    - `ErrorEventDetail`
7. Action:
    - `CreateErrorAction` — POST /{token}/errors (публичный)
    - `GetErrorGroupsAction` — GET /v1/projects/{projectId}/errors
    - `GetErrorGroupAction` — GET /v1/projects/{projectId}/errors/{groupId}
    - `GetErrorEventsAction` — GET /v1/projects/{projectId}/errors/{groupId}/events
    - `GetErrorEventAction` — GET /v1/projects/{projectId}/errors/{groupId}/events/{eventId}
    - `DeleteErrorGroupAction` — DELETE /v1/projects/{projectId}/errors/{groupId}
8. Роуты — добавить в `config/app.php`
10. Frontend docs — `docs/frontend/TrackerError.md`

Миграции НЕ создавать.