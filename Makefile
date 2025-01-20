.PHONY: start
start:
	make stop
	docker compose up --remove-orphans --timestamps --build --detach

.PHONY: stop
stop:
	docker compose down --remove-orphans

.PHONY: run-api-e2e-tests
run-api-e2e-tests:
	make start
	docker compose exec php-fpm bin/phpunit tests/E2E

.PHONY: run-api-unit-tests
run-api-unit-tests:
	make start
	docker compose exec php-fpm bin/phpunit tests/Unit
