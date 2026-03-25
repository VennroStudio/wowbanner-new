# auth-frontend-wowbanner

React + TypeScript фронтенд. Сборка через Vite, окружение через Docker.

## Структура проекта

```
auth-frontend/
├── docker/
│   ├── development/
│   │   └── Dockerfile        # dev-контейнер (Vite dev server)
│   └── production/
│       ├── Dockerfile        # prod-образ (сборка + nginx)
│       └── nginx.conf        # конфиг nginx для SPA
├── src/                      # исходный код
├── .env                      # переменные окружения (не коммитить)
├── .env.deploy               # переменные для деплоя (не коммитить)
├── .env.example              # шаблон .env
├── docker-compose.yml        # dev-окружение
├── docker-compose-production.yml  # prod-окружение (registry)
└── Makefile                  # команды управления проектом
```

## Первый запуск

### 1. Создать Vite-проект (если src/ ещё не существует)

```bash
make create
```

Добавить в `vite.config.ts`:
```ts
export default defineConfig({
  server: {
    host: '0.0.0.0',
    port: 5173,
  },
})
```

### 2. Поднять контейнер

```bash
make init
```
---
## Зависимости

```bash
make install              # установить все зависимости
make npm-add PACK=axios   # добавить пакет
make npm-remove PACK=axios # удалить пакет
```

## Качество кода

```bash
make lint    # проверка ESLint
make format  # форматирование Prettier
```

---

## Nginx Proxy Manager

Контейнер работает в сети `proxy`. Чтобы открыть через домен:

- **Domain**: `auth.wowbanner.local`
- **Forward Hostname**: `auth-frontend-wowbanner`
- **Forward Port**: `5173` (dev) / `80` (prod)