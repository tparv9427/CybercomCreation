# ═══════════════════════════════════════════════════════════
#  Reporting System — Makefile
#  Replaces setup.sh with standard, composable make targets.
#  Usage: make <target>
# ═══════════════════════════════════════════════════════════

.PHONY: help up down build restart logs ps \
        install migrate key-gen cache-clear \
        consume queue sample ingest solr-status \
        fe-install fe-dev fe-build

# ── Default: show help ───────────────────────────────────────────────────────
help:
	@echo ""
	@echo "  ╔══════════════════════════════════════════════╗"
	@echo "  ║     Reporting System — Available Commands    ║"
	@echo "  ╚══════════════════════════════════════════════╝"
	@echo ""
	@echo "  DOCKER"
	@echo "    make up          — Start all containers (detached)"
	@echo "    make down        — Stop all containers"
	@echo "    make build       — Rebuild and start all containers"
	@echo "    make restart     — Stop → Rebuild → Start"
	@echo "    make logs        — Tail logs from all containers"
	@echo "    make ps          — Show container status"
	@echo ""
	@echo "  BACKEND"
	@echo "    make install     — Install Laravel (Composer) dependencies"
	@echo "    make key-gen     — Generate Laravel APP_KEY"
	@echo "    make migrate     — Run database migrations"
	@echo "    make cache-clear — Clear all Laravel caches"
	@echo "    make sample      — Generate a sample 10k-row CSV"
	@echo "    make ingest      — Ingest CSV via API → Queue → Kafka → Solr"
	@echo "    make consume     — (Debug) Start Kafka consumer manually"
	@echo "    make queue       — (Debug) Start Laravel queue worker manually"
	@echo "    make solr-status — Check Solr collection document count"
	@echo ""
	@echo "  FRONTEND"
	@echo "    make fe-install  — Install npm dependencies"
	@echo "    make fe-dev      — Start Vite dev server"
	@echo "    make fe-build    — Build production bundle"
	@echo ""

# ── Docker ───────────────────────────────────────────────────────────────────
up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose up -d --build

restart:
	docker compose down && docker compose up -d --build

logs:
	docker compose logs -f --tail=80

ps:
	docker compose ps

# ── Backend ──────────────────────────────────────────────────────────────────
install:
	docker compose exec app bash -c "cd /var/www/html/backend && composer install"

key-gen:
	docker compose exec app bash -c "cd /var/www/html/backend && php artisan key:generate"

migrate:
	docker compose exec app bash -c "cd /var/www/html/backend && php artisan migrate --force"

cache-clear:
	docker compose exec app bash -c "cd /var/www/html/backend && php artisan config:clear && php artisan cache:clear && php artisan route:clear"

sample:
	docker compose exec app php /var/www/html/backend/scripts/generate_csv.php

# Trigger async CSV ingestion via API (recommended production path)
ingest:
	@echo "  → Upload a CSV via the dashboard at http://localhost:5173"
	@echo "  → Or use curl:"
	@echo "     curl -X POST http://localhost:9006/api/import/upload -F 'csv_file=@your_file.csv'"

# Manual consumer start (useful for debugging — normally runs as docker service)
consume:
	docker compose exec app bash -c "cd /var/www/html/backend && php artisan kafka:consume --batch-size=500"

# Manual queue worker (useful for debugging — normally runs as docker service)
queue:
	docker compose exec app bash -c "cd /var/www/html/backend && php artisan queue:work --tries=3"

solr-status:
	@curl -s "http://localhost:9007/solr/reports/select?q=*:*&rows=0" | python3 -m json.tool 2>/dev/null \
	|| curl -s "http://localhost:9007/solr/reports/select?q=*:*&rows=0"

# ── Frontend ─────────────────────────────────────────────────────────────────
fe-install:
	docker compose exec app bash -c "cd /var/www/html/frontend && npm install"

fe-dev:
	docker compose exec app bash -c "cd /var/www/html/frontend && npm run dev -- --host"

fe-build:
	docker compose exec app bash -c "cd /var/www/html/frontend && npm run build"
