.PHONY: up down logs restart

up:
	docker compose up -d

down:
	docker compose down -v

logs:
	docker compose logs -f

restart:
	make down
	make up