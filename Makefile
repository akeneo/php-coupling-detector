.PHONY: test

DOCKER_COMPOSE = docker compose
DOCKER_RUN = $(DOCKER_COMPOSE) run --rm php

install:
	$(DOCKER_COMPOSE) build --no-cache
	${MAKE} vendor -B

vendor:
	${DOCKER_RUN} composer install --no-interaction

test:
	${MAKE} vendor
	${DOCKER_RUN} vendor/bin/phpspec run
	${DOCKER_RUN} vendor/bin/phpstan analyse src --level 5
