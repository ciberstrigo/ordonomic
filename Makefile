# Определение переменных
DOCKER_COMPOSE = docker-compose
PHP_CONTAINER = webserver
CODE_PATH = /var/www/html/
CS_FIXER_PATH = vendor/friendsofphp/php-cs-fixer/php-cs-fixer

# Список правил

.PHONY: start
up:
	$(DOCKER_COMPOSE) up -d

down:
	$(DOCKER_COMPOSE) down

fix:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php $(CODE_PATH)$(CS_FIXER_PATH) fix $(CODE_PATH)

shell:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) sh

e:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) sh -c '$(CMD)'

operator__webhook_update:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) sh -c 'bin/console Telegram\\UpdateWebhook forRemittanceOperator'

.PHONY: help start stop fix shell operator__webhook_update