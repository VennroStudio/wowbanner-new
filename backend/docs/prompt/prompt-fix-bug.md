# Примеры промптов: Исправление ошибок

---

## Пример Промпт: Исправление ошибки Action запроса

````
Изучи правила проекта в docs/agent-rules.md.

## Проблема

При обновлении профиля пользователя (`PATCH /v1/user/update`) если передать email, 
который уже занят другим пользователем, возвращается 500 вместо 409 с ошибкой.

## Воспроизведение

- Метод: `PATCH`
- URL: `/v1/user/update`
- Заголовок: `Authorization: Bearer <token>`
- Тело:
```json
{
  "email": "existing@example.com",
  "firstName": "Иван",
  "lastName": "Иванов"
}
```
- Ответ: `500 Internal Server Error`
- Ожидаемый ответ: `409 { "error": { "code": 1, "message": "Email already registered." } }`

## Контекст

- Модуль: `User`
- Затронутые файлы: 
  - src/Modules/User/Command/User/Update/UpdateUserHandler.php

## Требования к исправлению

1. Исправь ошибку, не меняя существующую архитектуру и паттерны проекта.
2. Добавь проверку на уникальность email в Handler перед обновлением.
3. Используй DomainExceptionModule для ошибки.
4. Обнови docs/frontend/User.md если изменился контракт API.
````

---

## Пример Промпт: Исправление ошибки CommandHandler

````
Изучи правила проекта в docs/agent-rules.md.

## Проблема

При создании пользователя (`POST /v1/user/create`) Handler создаёт UserToken 
напрямую через UserTokenRepository вместо вызова CreateUserTokenHandler.
Это нарушает правило разделения Handler'ов по сущностям.

## Контекст

- Модуль: `User`
- Файл с ошибкой: `src/Modules/User/Command/User/Create/CreateUserHandler.php`
- Сейчас: Handler напрямую вызывает `$this->userTokenRepository->add($token)`
- Должно быть: Handler вызывает `$this->createUserTokenHandler->handle(new CreateUserTokenCommand(...))`

## Требования к исправлению

1. Убери прямую зависимость от `UserTokenRepository` в `CreateUserHandler`.
2. Добавь зависимость от `CreateUserTokenHandler` в конструктор.
3. Вынеси создание токена в отдельный приватный метод `createVerificationToken()`.
4. Метод `handle()` должен читаться как сценарий верхнего уровня.
5. Не меняй логику самого `CreateUserTokenHandler`.
6. Следуй правилам декомпозиции Handler из docs/agent-rules.md.
````

---

## Пример Промпт: Исправление ошибки FetcherQuery

````
Изучи правила проекта в docs/agent-rules.md.

## Проблема

Фетчер `UserFindAllFetcher` нарушает правила проекта: маппит строки из БД 
в сырые массивы вместо ReadModel DTO. По правилам все Fetcher'ы должны 
возвращать ReadModel объекты через `fromRow()` / `fromRows()`.

## Контекст

- Модуль: `User`
- Файл с ошибкой: `src/Modules/User/Query/User/FindAll/UserFindAllFetcher.php`
- Сейчас: `array_map` внутри Fetcher формирует массив вручную
- Должно быть: маппинг через ReadModel DTO с методами `fromRow()` и `fromRows()`

## Что нужно сделать

1. Создай ReadModel DTO `UserListItem` в `src/Modules/User/ReadModel/User/UserListItem.php`
   - `final readonly class` реализующий `UserViewInterface`
   - Метод `fromRow(array $row): self`
   - Метод `fromRows(array $rows): array`
   - Метод `toArray(): array`
   - Поля: id, role, firstName, lastName, email, createdAt, isActive

2. Исправь `UserFindAllFetcher`:
   - Убери ручной `array_map` с формированием массивов
   - Используй `UserListItem::fromRows($rows)` 
   - Обнови PHPDoc возвращаемого типа

3. Не меняй существующую логику фильтрации, пагинации и подсчёта.
4. Следуй всем правилам из docs/agent-rules.md.
````

---

## Пример Промпт: Исправление ошибки Entity

````
Изучи правила проекта в docs/agent-rules.md.

## Проблема

Сущность `User` позволяет редактировать удалённого пользователя — метод `edit()` 
не проверяет `deletedAt` перед изменением полей. По правилам все методы изменения 
состояния должны вызывать `assertNotDeleted()`.

## Контекст

- Модуль: `User`
- Файл с ошибкой: `src/Modules/User/Entity/User/User.php`
- Метод: `edit()`
- Сейчас: метод просто меняет поля без проверки
- Должно быть: вызов `$this->assertNotDeleted()` в начале метода

## Требования к исправлению

1. Добавь вызов `$this->assertNotDeleted()` в начало метода `edit()`.
2. Убедись что `assertNotDeleted()` бросает `DomainExceptionModule` с правильным ключом перевода.
3. Проверь что ключ `error.user_is_deleted` существует в Translation (en + ru).
4. Если ключа нет — добавь в `errors.en.php` и `errors.ru.php`.
5. Не меняй другие методы сущности.
6. Следуй правилам Entity из docs/agent-rules.md.
````
