-include .env.deploy
export

SERVICE = crm-app-wowbanner
VERSION := $(IMAGE_TAG).$(BUILD_NUMBER)

# ─── Setup ────────────────────────────────────────────────────────────────────
create:
	docker run --rm -v "$(CURDIR):/app" -w /app node:22-alpine sh -c "npx --yes create-vite@latest temp-app --template react-ts && cp -r temp-app/* . && cp -r temp-app/.[!.]* . 2>/dev/null || true && rm -rf temp-app && rm -f package-lock.json"
	docker run --rm -v "$(CURDIR):/app" -w /app node:22-alpine npm install -D tailwindcss @tailwindcss/vite
	docker run --rm -v "$(CURDIR):/app" -w /app node:22-alpine npm install
	docker run --rm -v "$(CURDIR):/app" -w /app node:22-alpine npm install react-hook-form zod @hookform/resolvers axios @tanstack/react-query zustand lucide-react

# ─── Development ──────────────────────────────────────────────────────────────
init: docker-clear docker-build docker-up
restart: docker-down docker-up
rebuild: docker-down docker-build docker-up

docker-up:
	docker compose up -d $(SERVICE)

docker-down:
	docker compose down

docker-clear:
	docker compose down -v

docker-build:
	docker compose build --pull $(SERVICE)

# ─── Local ────────────────────────────────────────────────────────────────────
node-install:
	docker run --rm -v "$(CURDIR):/app" -w /app node:20-alpine npm install

install:
	docker compose run --rm $(SERVICE) npm install

npm-add:
	docker compose run --rm $(SERVICE) npm install $(PACK)

npm-remove:
	docker compose run --rm $(SERVICE) npm uninstall $(PACK)

dev:
	npm run dev

build-local:
	npm run build

preview:
	npm run preview

lint:
	docker compose run --rm $(SERVICE) npm run lint

format:
	docker compose run --rm $(SERVICE) npx prettier --write "src/**/*.{ts,tsx,css,json}"

docker-logs:
	docker compose logs -f $(SERVICE)

docker-shell:
	docker compose exec $(SERVICE) sh

# ─── Production ───────────────────────────────────────────────────────────────
docker-login:
	docker login ${REGISTRY}

server-login:
	ssh -t $(HOST) -p $(PORT) 'docker login $(REGISTRY)'

server-logout:
	ssh -t $(HOST) -p $(PORT) 'docker logout'

build:
	docker --log-level=debug build --platform linux/amd64 --pull \
		--file=./docker/production/Dockerfile \
		--tag=${REGISTRY}/$(PROJECT_NAME):${VERSION} .

push:
	docker push ${REGISTRY}/$(PROJECT_NAME):${VERSION}

deploy:
	ssh $(HOST) -p $(PORT) 'rm -rf $(PROJECT_NAME)_$(BUILD_NUMBER)'
	ssh $(HOST) -p $(PORT) 'mkdir $(PROJECT_NAME)_$(BUILD_NUMBER)'
	scp -P $(PORT) docker-compose-production.yml $(HOST):$(PROJECT_NAME)_$(BUILD_NUMBER)/docker-compose-production.yml
	scp -P $(PORT) .env.prod $(HOST):$(PROJECT_NAME)_$(BUILD_NUMBER)/.env
	ssh $(HOST) -p $(PORT) 'cd $(PROJECT_NAME)_$(BUILD_NUMBER) && echo "COMPOSE_PROJECT_NAME=$(PROJECT_NAME)" >> .env'
	ssh $(HOST) -p $(PORT) 'cd $(PROJECT_NAME)_$(BUILD_NUMBER) && echo "REGISTRY=$(REGISTRY)" >> .env'
	ssh $(HOST) -p $(PORT) 'cd $(PROJECT_NAME)_$(BUILD_NUMBER) && echo "IMAGE_TAG=$(VERSION)" >> .env'
	ssh $(HOST) -p $(PORT) 'cd $(PROJECT_NAME)_$(BUILD_NUMBER) && docker compose -f docker-compose-production.yml pull'
	ssh $(HOST) -p $(PORT) 'cd $(PROJECT_NAME)_$(BUILD_NUMBER) && docker compose -f docker-compose-production.yml down'
	ssh $(HOST) -p $(PORT) 'cd $(PROJECT_NAME)_$(BUILD_NUMBER) && docker compose -f docker-compose-production.yml up -d'
	ssh $(HOST) -p $(PORT) 'rm -f $(PROJECT_NAME)'
	ssh $(HOST) -p $(PORT) 'ln -sr $(PROJECT_NAME)_$(BUILD_NUMBER) $(PROJECT_NAME)'

rollback:
	ssh $(HOST) -p $(PORT) 'cd $(PROJECT_NAME)_$(BUILD_NUMBER) && docker compose -f docker-compose-production.yml pull'
	ssh $(HOST) -p $(PORT) 'cd $(PROJECT_NAME)_$(BUILD_NUMBER) && docker compose -f docker-compose-production.yml up --build --remove-orphans -d'
	ssh $(HOST) -p $(PORT) 'rm -f $(PROJECT_NAME)'
	ssh $(HOST) -p $(PORT) 'ln -sr $(PROJECT_NAME)_$(BUILD_NUMBER) $(PROJECT_NAME)'

list-v:
	ssh $(HOST) -p $(PORT) 'ls -lht $(PROJECT_NAME)_* 2>/dev/null || echo "Версии не найдены"'
	ssh $(HOST) -p $(PORT) 'readlink $(PROJECT_NAME) 2>/dev/null || echo "Символическая ссылка не найдена"'

clean-v:
	ssh $(HOST) -p $(PORT) 'cd ~ && ls -dt $(PROJECT_NAME)_* | tail -n +$$((${KEEP_VERSIONS} + 1)) | xargs -r rm -rf'

# ─── Help ─────────────────────────────────────────────────────────────────────
help:
	@echo "======================================"
	@echo "-SETUP:"
	@echo "  make create            - Создать Vite React+TS проект"
	@echo "  make init              - Первый запуск (сборка + поднятие контейнеров)"
	@echo ""
	@echo "-DEVELOPMENT:"
	@echo "  make restart           - Рестарт dev-контейнеров"
	@echo "  make rebuild           - Пересборка и перезапуск"
	@echo "  make docker-logs       - Логи контейнера"
	@echo "  make docker-shell      - Войти в контейнер"
	@echo ""
	@echo "-NPM:"
	@echo "  make install           - Установить зависимости"
	@echo "  make npm-add PACK=     - Добавить пакет"
	@echo "  make npm-remove PACK=  - Удалить пакет"
	@echo "  make lint              - Проверка ESLint"
	@echo "  make format            - Форматирование Prettier"
	@echo ""
	@echo "-PRODUCTION:"
	@echo "--Registry:"
	@echo "  make docker-login      - Авторизация локального докера в registry"
	@echo "  make server-login      - Авторизация удалённого докера в registry"
	@echo ""
	@echo "--Deploy:"
	@echo "  make build             - Собрать production образ"
	@echo "  make push              - Загрузить образ в registry"
	@echo "  make deploy            - Деплой на сервер"
	@echo "  make rollback          - Откат на предыдущую версию"
	@echo "  make list-v            - Список версий на сервере"
	@echo "  make clean-v           - Очистка старых версий"
	@echo "======================================"

.PHONY: help create init restart rebuild docker-up docker-down docker-clear docker-build \
        docker-logs docker-shell install npm-add npm-remove dev build-local preview lint format \
        docker-login server-login server-logout build push deploy rollback list-v clean-v