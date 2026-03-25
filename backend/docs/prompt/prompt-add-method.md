# Промпт: Добавление нового метода к существующей сущности

````
Изучи правила проекта в docs/agent-rules.md.

## Задача

Добавь метод «Смена пароля» для сущности `User` в модуле `User`.

## Описание

Авторизованный пользователь может изменить свой пароль, указав текущий и новый пароли.

## API

- Метод: `PATCH`
- URL: `/v1/user/change-password`
- Защищённый: да
- Тело:
```json
{
  "currentPassword": "OldSecret123!",
  "newPassword": "NewSecret456!"
}
```
- Успешный ответ: `200`
```json
{
  "data": {
    "success": 1
  }
}
```

## Валидация полей

| Поле | Правила |
|---|---|
| currentPassword | не пустое |
| newPassword | 8-64 символа, заглавная + строчная + цифра + спецсимвол |

## Бизнес-правила

- Текущий пароль должен совпадать с сохранённым
- Новый пароль не должен совпадать с текущим

## Ошибки

| Ситуация | error.code | error.message |
|---|---|---|
| Неверный текущий пароль | 10 | error.invalid_current_password |
| Новый пароль совпадает со старым | 11 | error.password_same_as_current |

## Что создать

1. **ChangePasswordCommand** + **ChangePasswordHandler** — проверка текущего пароля, хеширование нового
2. **ChangePasswordAction** — PATCH /v1/user/change-password, защищённый
3. **Translation** — добавить ключи ошибок в errors.en.php, errors.ru.php
4. **Маршрут** — добавить в config/routes/v1.php
5. **Frontend docs** — дополнить docs/frontend/User.md

Миграции НЕ создавать.
````
