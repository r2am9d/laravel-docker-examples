SHELL := /usr/bin/env bash

# Default variables
NODE_VERSION := 22.0.0
DC := docker compose -f compose.dev.yaml

# Common workspace command wrapper
define WS_EXEC
$(DC) exec -it workspace bash -c "source ~/.nvm/nvm.sh && nvm use $(NODE_VERSION) > /dev/null && $(1)"
endef

# ---------- ENV BUILD ----------

build_env:
	bin/build_env.sh

docker_config: build_env
	$(DC) config

images:
	$(DC) images

# ---------- CONTAINER MANAGEMENT ----------

docker_build:
	$(DC) build

up: build_env composer up_nobuild artisan_run_migrations npm_run_dev pre_push

up_nobuild: install_artisan_encryption_key
	$(DC) up -d --force-recreate --remove-orphans
	bin/wait_for_docker.bash "database system is ready to accept connections"

down:
	$(DC) down

down_ci:
	$(DC) down || exit 0

start:
	$(DC) start
	$(call WS_EXEC,npm run dev)

stop:
	$(DC) stop

restart:
	$(DC) up -d --force-recreate --remove-orphans
	$(DC) start
	$(call WS_EXEC,npm run dev)

# ---------- DEVELOPMENT COMMANDS ----------

bash:
	$(DC) run --rm workspace bash

composer:
	$(DC) run --rm workspace bash -c "composer install"

install_artisan_encryption_key:
	$(DC) run --rm workspace bash -c "php artisan key:generate"

artisan_run_migrations:
	$(DC) exec workspace php artisan migrate

npm_run_dev:
	$(call WS_EXEC,npm install && npm run dev)

queue:
	$(DC) exec -it php-fpm php artisan queue:work

pre_push:
	$(DC) exec -it workspace bash -c "./bin/pre_push.sh"

logs_tail: build_env
	$(DC) logs -f

# ---------- CLEANUP ----------

docker_clean:
	@echo "Stopping all containers (except registry-cache)..."
	@for id in $$(docker ps -q); do \
		name=$$(docker inspect -f '{{.Name}}' $$id 2>/dev/null | sed 's|^/||'); \
		if [ "$$name" != "registry-cache" ]; then \
			docker stop $$id; \
		fi; \
	done && \
	echo "Removing all containers (except registry-cache)..." && \
	for id in $$(docker ps -aq); do \
		name=$$(docker inspect -f '{{.Name}}' $$id 2>/dev/null | sed 's|^/||'); \
		if [ "$$name" != "registry-cache" ]; then \
			docker rm $$id; \
		fi; \
	done && \
	echo "Removing all images (except registry:2)..." && \
	for img in $$(docker images -q); do \
		tags=$$(docker inspect -f '{{join .RepoTags ","}}' $$img 2>/dev/null); \
		if ! echo "$$tags" | grep -q "registry:2"; then \
			docker rmi -f $$img || true; \
		fi; \
	done && \
	echo "Removing all user-created networks (excluding defaults and registry-cache)..." && \
	for net in $$(docker network ls --filter "type=custom" -q); do \
		name=$$(docker network inspect -f '{{.Name}}' $$net 2>/dev/null); \
		attached=$$(docker network inspect -f '{{range .Containers}}{{.Name}} {{end}}' $$net); \
		if [ "$$name" != "bridge" ] && [ "$$name" != "host" ] && [ "$$name" != "none" ] && [ "$$name" != "registry-cache" ] && echo "$$attached" | grep -vq "registry-cache"; then \
			docker network rm $$net; \
		fi; \
	done && \
	echo "Removing all user-created local volumes (excluding those in use and registry-cache)..." && \
	for vol in $$(docker volume ls -q); do \
		name=$$(docker volume inspect -f '{{.Name}}' $$vol 2>/dev/null); \
		scope=$$(docker volume inspect -f '{{.Scope}}' $$vol 2>/dev/null); \
		in_use=$$(docker ps -a --filter volume=$$name -q); \
		if [ "$$name" != "registry-cache" ] && [ "$$scope" = "local" ] && [ -z "$$in_use" ]; then \
			docker volume rm $$vol; \
		fi; \
	done