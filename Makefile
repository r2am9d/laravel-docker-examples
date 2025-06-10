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
	docker ps -q | xargs -r docker stop && \
	docker ps -aq | xargs -r docker rm && \
	docker images -q | xargs -r docker rmi -f && \
	docker network ls --filter "type=custom" -q | xargs -r docker network rm && \
	for vol in $$(docker volume ls -q); do \
		if [ "$$(docker volume inspect -f '{{.Scope}}' $$vol)" = "local" ]; then \
			docker volume rm $$vol; \
		fi; \
	done
