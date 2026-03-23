# Reporting System

A production-ready dynamic reporting system built with **Laravel** + **Vue 3** + **Apache Solr** + **Apache Kafka** + **MySQL** + **Redis** — all orchestrated via Docker.

---

## Architecture Pipeline

```
CSV Upload (Dashboard)
  → POST /api/import/upload   (Laravel ImportController)
  → ProcessCsvBatch Job       (Redis Queue Worker, async batches of 1000 rows)
  → KafkaService::produce()   (rdkafka extension)
  → Kafka Topic               (report_data_topic)
  → php artisan kafka:consume (Artisan Daemon, batches of 500 → Solr)
  → Apache Solr               (indexed, searchable documents)
  → GET /api/report/*         (Laravel API, queries Solr)
  → Vue Dashboard             (Charts, Table, Comparison)
```

---

## Prerequisites

| Tool           | Download                                       |
|----------------|------------------------------------------------|
| Docker Desktop | https://www.docker.com/products/docker-desktop |
| GNU Make       | Pre-installed on Mac/Linux. Windows: use Git Bash or WSL |

---

## Services & Ports

| Service            | Port | URL                                  |
|--------------------|------|--------------------------------------|
| App (Nginx + PHP)  | 9006 | http://localhost:9006                |
| Frontend (Vite)    | 5173 | http://localhost:5173                |
| Solr Admin UI      | 9007 | http://localhost:9007/solr           |
| MySQL              | 9008 | any MySQL client                     |
| Kafka Broker       | 9009 | internal                             |
| Redis              | 9010 | internal                             |

---

## First Time Setup

```bash
# 1. Build and start all 7 containers
make build

# 2. Install Laravel dependencies
make install

# 3. Generate app key
make key-gen

# 4. Run database migrations
make migrate

# 5. Install frontend dependencies
make fe-install

# 6. Start the frontend dev server
make fe-dev

# 7. Open the dashboard
#    http://localhost:5173
```

---

## Available Commands

```bash
make help          # Show all commands

# Docker
make up            # Start all containers
make down          # Stop all containers
make build         # Rebuild & start
make restart       # Stop → Rebuild → Start
make logs          # Tail all container logs
make ps            # Container status

# Backend
make install       # Composer install
make key-gen       # Generate APP_KEY
make migrate       # Run migrations
make cache-clear   # Clear Laravel caches
make sample        # Generate a sample 10k-row CSV
make ingest        # Show curl command for ingesting CSV
make solr-status   # Check Solr document count

# Frontend
make fe-install    # npm install
make fe-dev        # Start Vite dev server
make fe-build      # Build for production
```

---

## Data Ingestion (Production)

Upload any CSV file via the dashboard or directly via API:

```bash
curl -X POST http://localhost:9006/api/import/upload \
     -F 'csv_file=@/path/to/your/data.csv'
```

The system will:
1. Validate and parse the CSV without loading it all into memory
2. Dispatch async background jobs (1,000 rows per batch) via **Redis Queue**
3. Each job uses `KafkaService` to stream rows into Kafka
4. The `kafka-consumer` Docker service continuously indexes batches into **Solr**

---

## Credentials

| Service  | Detail           |
|----------|------------------|
| MySQL DB | reporting        |
| MySQL User | reporting_user |
| MySQL Pass | reporting_pass |
| MySQL Root | root           |

---

## Folder Structure

```
reporting-system/
├── docker/
│   └── php/
│       ├── Dockerfile
│       └── nginx.conf
├── backend/                    ← Laravel PHP API
│   ├── app/
│   │   ├── Console/Commands/
│   │   │   └── KafkaConsumeCommand.php   ← Kafka Consumer Daemon
│   │   ├── Http/Controllers/
│   │   │   └── ImportController.php      ← CSV Upload API
│   │   ├── Jobs/
│   │   │   └── ProcessCsvBatch.php       ← Async Batch Job
│   │   └── Services/
│   │       ├── KafkaService.php          ← Kafka Producer Service
│   │       └── SolrClient.php
│   └── scripts/
│       └── kafka_producer.php            ← ⚠️ DEPRECATED (kept as reference)
├── frontend/                   ← Vue 3 + Vite
│   └── src/components/
│       └── ChartRenderer.vue             ← Bar/Pie/Line Charts
├── solr/                       ← Solr config
├── Makefile                    ← All project commands
├── docker-compose.yml          ← 7-service orchestration
└── README.md
```