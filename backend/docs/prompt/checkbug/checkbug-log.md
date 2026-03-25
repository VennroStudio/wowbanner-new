Изучи правила проекта в docs/agent-rules.md.

## Задача
Создай сущности `LogGroup` и `LogEvent` в модуле `src/Modules/Tracker`.

## Описание
SDK шлёт лог на POST /{token}/logs.
Логи группируются по уникальной паре (projectId + message + level).
Одна группа = одна строка в списке со счётчиком событий.
Каждый отдельный вызов ->info(...) = одно событие (LogEvent).

Пример: если ->info('User logged in', [...]) вызвали 100 раз —
в списке будет 1 строка "User logged in" с числом 100 справа.
При клике — открывается детальная страница:
слева JSON контекст выбранного события, справа список всех 100 событий.

## Сущность `LogGroup`

| Поле        | Тип               | Обязательное | Описание                                      |
|-------------|-------------------|--------------|-----------------------------------------------|
| id          | int (auto)        | да           | Первичный ключ                                |
| projectId   | int               | да           | ID проекта (не ORM relation)                  |
| level       | LogLevel (enum)   | да           | DEBUG=1, INFO=2, WARN=3, ERROR=4, FATAL=5     |
| message     | string (text)     | да           | Текст лога — ключ группировки                 |
| firstSeenAt | DateTimeImmutable | да           | Время первого события                         |
| lastSeenAt  | DateTimeImmutable | да           | Время последнего события (обновляется)        |

## Сущность `LogEvent`

| Поле        | Тип               | Обязательное | Описание                                      |
|-------------|-------------------|--------------|-----------------------------------------------|
| id          | int (auto)        | да           | Первичный ключ                                |
| groupId     | int               | да           | ID группы (не ORM relation)                   |
| projectId   | int               | да           | ID проекта (не ORM relation)                  |
| context     | json              | нет          | Массив context из SDK                         |
| ip          | string (45)       | нет          | IP клиента                                    |
| url         | text              | нет          | URL запроса                                   |
| method      | string (16)       | нет          | HTTP метод                                    |
| headers     | json              | нет          | Заголовки запроса                             |
| queryParams | json              | нет          | Query параметры                               |
| bodyParams  | json              | нет          | Body параметры                                |
| cookies     | json              | нет          | Cookies                                       |
| session     | json              | нет          | Session                                       |
| files       | json              | нет          | Files                                         |
| createdAt   | DateTimeImmutable | да           | Время события (из поля `time` в payload SDK)  |

## Payload от SDK (POST /{token}/logs)

{
"time":        1712345678000,   ← createdAt события (миллисекунды)
"level":       "INFO",
"message":     "User logged in",
"context":     { "userId": 8 },
"ip":          "1.2.3.4",
"url":         "https://app.example.com/login",
"method":      "POST",
"headers":     { ... },
"queryParams": null,
"bodyParams":  { "email": "...", "password": "***" },
"cookies":     null,
"session":     null,
"files":       null
}

## Методы API

### SDK (публичные, без авторизации)

#### POST /{token}/logs
- Найти Project по token через `ProjectFindByTokenFetcher`
- Если не найден или `deletedAt IS NOT NULL` — вернуть 404
- Найти LogGroup по `projectId + message + level` через `LogGroupFindByKeyFetcher`
- Если не найдена — создать через `CreateLogGroupHandler`
- Создать LogEvent через `CreateLogEventHandler`
- Обновить `lastSeenAt` у LogGroup через `UpdateLogGroupHandler`
- Ответ: 201 { "data": { "ok": true } }

### Авторизованные (Bearer JWT, владелец проекта)

#### GET /v1/projects/{projectId}/logs
- Проверить владельца проекта: `ProjectGetByIdFetcher` → если `userId != identity->id` → `AccessDeniedException`
- Список LogGroup по projectId с количеством событий (total, 24ч, 7д, 30д)
- Счётчики считать через COUNT в подзапросах, НЕ хранить в таблице
- Фильтры: `level` (enum string), `search` (по message ILIKE), `dateFrom`, `dateTo` (по lastSeenAt)
- Пагинация: `page`, `perPage`
- Ответ:
  200 {
  "data": {
  "count": N,
  "items": [
  {
  "id": 1,
  "level": { "id": 2, "label": "Info", "color": "blue" },
  "message": "User logged in",
  "totalCount": 100,
  "count24h": 12,
  "count7d": 45,
  "count30d": 100,
  "firstSeenAt": "2025-07-24 17:23:00",
  "lastSeenAt": "2025-07-24 17:26:00"
  }
  ]
  }
  }

#### GET /v1/projects/{projectId}/logs/{groupId}
- Проверить владельца проекта
- Детальная информация о группе + счётчики 24ч/7д/30д
- Ответ: 200 { "data": { ...группа с счётчиками... } }

#### GET /v1/projects/{projectId}/logs/{groupId}/events
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
  "context": { "userId": 8 },
  "ip": "1.2.3.4",
  "url": "https://...",
  "method": "POST",
  "createdAt": "2025-07-24 17:26:00"
  }
  ]
  }
  }

#### GET /v1/projects/{projectId}/logs/{groupId}/events/{eventId}
- Проверить владельца проекта
- Детальное событие со всеми полями (headers, bodyParams и тд)
- Ответ: 200 { "data": { ...все поля события... } }

#### DELETE /v1/projects/{projectId}/logs/{groupId}
- Проверить владельца проекта
- Удалить все LogEvent этой группы
- Удалить LogGroup
- Физическое удаление (не soft delete)
- Ответ: 200 { "data": { "success": 1 } }

## Бизнес-правила
- Группировка по уникальному ключу: projectId + message + level
- `firstSeenAt` устанавливается один раз при создании группы
- `lastSeenAt` обновляется при каждом новом событии
- Счётчики 24ч/7д/30д — всегда COUNT из log_events, никогда не хранить
- Удаление группы — каскадное: сначала все события, потом группа
- Проверка владельца проекта — в каждом авторизованном Action

## Enum LogLevel
DEBUG=1, INFO=2, WARN=3, ERROR=4, FATAL=5
fromString(): 'DEBUG'→DEBUG, 'INFO'/'NOTICE'→INFO,
'WARN'/'WARNING'→WARN, 'ERROR'→ERROR,
'FATAL'/'CRITICAL'/'ALERT'/'EMERGENCY'→FATAL

## Что создать
Следуя docs/agent-rules.md, создай все компоненты:

1. Enum — `LogLevel` (`src/Modules/Tracker/Entity/LogGroup/Fields/Enums/LogLevel.php`)
2. Entity — `LogGroup` (`src/Modules/Tracker/Entity/LogGroup/LogGroup.php`)
    - Методы: `create()`, `updateLastSeenAt(DateTimeImmutable $at)`
3. Entity — `LogEvent` (`src/Modules/Tracker/Entity/LogEvent/LogEvent.php`)
    - Метод: `create()`
4. Repository:
    - `LogGroupRepository` + `DoctrineLogGroupRepository`
    - `LogEventRepository` + `DoctrineLogEventRepository`
5. Command/Handler:
    - `CreateLogGroupCommand/Handler` — создать группу
    - `CreateLogEventCommand/Handler` — создать событие
    - `UpdateLogGroupLastSeenCommand/Handler` — обновить lastSeenAt
    - `DeleteLogGroupCommand/Handler` — удалить группу + все её события
6. Query/Fetcher:
    - `LogGroupFindByKeyQuery/Fetcher` — поиск по (projectId + message + level), возвращает ?LogGroupByKey
    - `LogGroupFindAllQuery/Fetcher` — список групп с COUNT событий (total, 24h, 7d, 30d)
    - `LogGroupGetByIdQuery/Fetcher` — детальная группа с COUNT
    - `LogEventFindAllQuery/Fetcher` — список событий группы
    - `LogEventGetByIdQuery/Fetcher` — детальное событие
7. ReadModel:
    - `LogGroupViewInterface`
    - `LogGroupListItem` — для списка (с totalCount, count24h, count7d, count30d)
    - `LogGroupDetail` — детальная группа (с теми же счётчиками)
    - `LogEventViewInterface`
    - `LogEventListItem` — для списка событий
    - `LogEventDetail` — детальное событие со всеми полями
8. Action:
    - `CreateLogAction` — POST /{token}/logs (публичный)
    - `GetLogGroupsAction` — GET /v1/projects/{projectId}/logs
    - `GetLogGroupAction` — GET /v1/projects/{projectId}/logs/{groupId}
    - `GetLogEventsAction` — GET /v1/projects/{projectId}/logs/{groupId}/events
    - `GetLogEventAction` — GET /v1/projects/{projectId}/logs/{groupId}/events/{eventId}
    - `DeleteLogGroupAction` — DELETE /v1/projects/{projectId}/logs/{groupId}
9. Роуты — добавить в `config/app.php`
10. Frontend docs — `docs/frontend/TrackerLog.md`

Миграции НЕ создавать.