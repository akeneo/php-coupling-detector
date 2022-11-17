.PHONY: test

DOCKER_RUN = docker-compose run --rm php

vendor:
	${DOCKER_RUN} composer install --no-interaction

test:
	${MAKE} vendor
	${DOCKER_RUN} vendor/bin/phpspec run
	${DOCKER_RUN} vendor/bin/phpstan analyse src --level 5
