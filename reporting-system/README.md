# Reporting System

A dynamic reporting system built with PHP (Laravel) + React + Solr + Kafka + MySQL.

---

## Prerequisites

Make sure these are installed before starting:

| Tool | Download |
|------|----------|
| Docker Desktop | https://www.docker.com/products/docker-desktop |
| Git Bash (Windows only) | https://git-scm.com/downloads |
| Node.js 20+ | https://nodejs.org |

---

## Project Ports

| Service | Host Port | Access |
|---------|-----------|--------|
| App (Nginx + PHP API) | 9006 | http://localhost:9006 |
| Solr Admin UI | 9007 | http://localhost:9007/solr |
| MySQL | 9008 | use any MySQL client |
| Kafka | 9009 | internal broker |

---

## How to Run

### Windows
```bash
# Open Git Bash in the project folder and run:
bash setup.sh
```

### 5. Run Data Pipeline (Strict: CSV → Kafka → Solr)
To ingest the 16 CSV files from the `uploads` folder:
```bash
# 1. Run Producer (Processes all CSVs in uploads/ and sends to Kafka)
docker compose exec app php /var/www/html/backend/scripts/kafka_producer.php /var/www/html/backend/storage/app/uploads

# 2. Run Consumer (Indexes messages from Kafka to Solr)
docker compose exec app php /var/www/html/backend/scripts/kafka_consumer.php
```

### New Features:
- **Period Comparison:** Select two date ranges to compare metrics (e.g., Price) across groups (e.g., Brand).
- **Dynamic Field Mapping:** Automatically detects and maps CSV columns to Solr types.
- **Premium UI:** Revamped dashboard with glassmorphism and real-time visualization.

### Mac
```bash
chmod +x setup.sh
./setup.sh
```

### Linux
```bash
chmod +x setup.sh
./setup.sh
```

---

## First Time Setup (follow in order)
```
1. Run setup.sh
2. Choose [1] Docker → [3] Rebuild and start containers
3. Wait for all 4 containers to show "Up"
4. Choose [2] Backend → [1] Install Laravel dependencies
5. Choose [2] Backend → [2] Run database migrations
6. Choose [2] Backend → [3] Generate sample CSV
7. Choose [2] Backend → [4] Ingest CSV into Solr
8. Choose [3] Frontend → [1] Install dependencies
9. Choose [3] Frontend → [2] Start dev server
10. Open http://localhost:5173 in your browser
```

---

## Credentials

| Service | Detail |
|---------|--------|
| MySQL Host | localhost:9008 |
| MySQL Database | reporting |
| MySQL User | reporting_user |
| MySQL Password | reporting_pass |
| MySQL Root Password | root |

---

## Folder Structure
```
reporting-system/
├── docker/
│   └── php/
│       ├── Dockerfile
│       └── nginx.conf
├── backend/          ← Laravel PHP API
├── frontend/         ← React + Vite
├── solr/             ← Solr config
├── docker-compose.yml
├── setup.sh
└── README.md
```