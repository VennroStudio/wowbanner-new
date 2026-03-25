# Agent Rules

Единый справочник по структуре и паттернам проекта **vs-project**.

> **⚠️ Важно:** Примеры в правилах — ориентир, не шаблон для слепого копирования. У каждой сущности свои поля, связи, бизнес-правила и зависимости.

---

## Разделы

1. [Структура и окружение](rules/structure.md)
2. [Entity](rules/entity.md)
3. [Repository](rules/repository.md)
4. [Command / Handler](rules/commandhandler.md)
5. [Query / Fetcher](rules/fetcher.md)
6. [ReadModel](rules/readmodel.md)
7. [Service / Permission](rules/service.md)
8. [Action](rules/action.md)
9. [Translation](rules/translation.md)
10. [Components](rules/components.md)
11. [Исключения и HTTP-коды](rules/exceptions.md)
12. [Frontend API Docs](rules/frontend-api-docs.md)

---

## Миграции

> **⚠️ Миграции составлять НЕ нужно.** Делает разработчик самостоятельно через Doctrine Migrations.

Расположение: `src/Migrations/`