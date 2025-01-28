.PHONY: start
start:
	make stop
	docker compose up --remove-orphans --timestamps

.PHONY: stop
stop:
	docker compose down --remove-orphans

.PHONY: run-api-e2e-tests
run-api-e2e-tests:
	docker compose exec php-fpm bin/phpunit tests/E2E

.PHONY: run-api-unit-tests
run-api-unit-tests:
	docker compose exec php-fpm bin/phpunit tests/Unit

.PHONY: open
open:
	make open-app
	make open-docs

.PHONY: open-app
open-app:
	make start
	open http://localhost:8000 || xdg-open http://localhost:8000 || gnome-open http://localhost:8000

.PHONY: open-docs
open-docs:
	make start
	open http://localhost:8000/docs || xdg-open http://localhost:8000/docs || gnome-open http://localhost:8000/docs

.PHONY: logs
logs:
	docker compose logs -f