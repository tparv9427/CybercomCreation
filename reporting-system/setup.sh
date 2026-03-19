#!/bin/bash

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
WHITE='\033[1;37m'
DIM='\033[2m'
NC='\033[0m'

print_header() {
    clear
    echo -e "${CYAN}"
    echo "  ╔══════════════════════════════════════════════════════╗"
    echo "  ║         REPORTING SYSTEM — Setup & Control          ║"
    echo "  ╚══════════════════════════════════════════════════════╝"
    echo -e "${NC}"
}

print_divider() {
    echo -e "${DIM}  ──────────────────────────────────────────────────────${NC}"
}

press_any_key() {
    echo ""
    echo -e "${DIM}  Press Enter to return to menu...${NC}"
    read
}

# ─── DOCKER MENU ───────────────────────────────────────
menu_docker() {
    while true; do
        print_header
        echo -e "  ${WHITE}Docker Containers${NC}"
        echo ""
        echo -e "  ${GREEN}[1]${NC} Start all containers"
        echo -e "  ${GREEN}[2]${NC} Stop all containers"
        echo -e "  ${GREEN}[3]${NC} Rebuild and start all containers"
        echo -e "  ${GREEN}[4]${NC} View container status"
        echo -e "  ${GREEN}[5]${NC} View container logs"
        print_divider
        echo -e "  ${YELLOW}[0]${NC} Back to main menu"
        echo ""
        echo -ne "  Choose an option [0-5]: "
        read choice

        case $choice in
            1)
                print_header
                echo -e "  ${CYAN}▶ Starting all containers...${NC}"
                echo -e "  ${DIM}  CMD: docker compose up -d${NC}"
                echo ""
                docker compose up -d
                press_any_key
                ;;
            2)
                print_header
                echo -e "  ${CYAN}▶ Stopping all containers...${NC}"
                echo -e "  ${DIM}  CMD: docker compose down${NC}"
                echo ""
                docker compose down
                press_any_key
                ;;
            3)
                print_header
                echo -e "  ${CYAN}▶ Rebuilding and starting all containers...${NC}"
                echo -e "  ${DIM}  CMD: docker compose up -d --build${NC}"
                echo ""
                docker compose up -d --build
                press_any_key
                ;;
            4)
                print_header
                echo -e "  ${CYAN}▶ Container status:${NC}"
                echo -e "  ${DIM}  CMD: docker compose ps${NC}"
                echo ""
                docker compose ps
                press_any_key
                ;;
            5)
                print_header
                echo -e "  ${CYAN}▶ Container logs (last 50 lines):${NC}"
                echo -e "  ${DIM}  CMD: docker compose logs --tail=50${NC}"
                echo ""
                docker compose logs --tail=50
                press_any_key
                ;;
            0) break ;;
            *) echo -e "  ${RED}Invalid option.${NC}"; sleep 1 ;;
        esac
    done
}

# ─── BACKEND MENU ──────────────────────────────────────
menu_backend() {
    while true; do
        print_header
        echo -e "  ${WHITE}Backend — Laravel Setup${NC}"
        echo ""

        if [ -d "backend/vendor" ]; then
            echo -e "  ${GREEN}✓ Laravel dependencies installed${NC}"
        else
            echo -e "  ${RED}✗ Laravel dependencies NOT installed — run option [1]${NC}"
        fi

        if [ -f "backend/storage/sample_data.csv" ]; then
            echo -e "  ${GREEN}✓ Sample CSV exists${NC}"
        else
            echo -e "  ${RED}✗ Sample CSV not found — run option [4]${NC}"
        fi

        echo ""
        echo -e "  ${GREEN}[1]${NC} Install Laravel (first time / missing vendor)"
        echo -e "  ${GREEN}[2]${NC} Generate app key"
        echo -e "  ${GREEN}[3]${NC} Run database migrations"
        echo -e "  ${GREEN}[4]${NC} Generate sample CSV (10k rows)"
        echo -e "  ${GREEN}[5]${NC} Ingest CSV into Solr"
        echo -e "  ${GREEN}[6]${NC} Run Kafka producer"
        echo -e "  ${GREEN}[7]${NC} Clear Laravel caches"
        echo -e "  ${GREEN}[8]${NC} Check Solr collection status"
        print_divider
        echo -e "  ${YELLOW}[0]${NC} Back to main menu"
        echo ""
        echo -ne "  Choose an option [0-8]: "
        read choice

        case $choice in
            1)
                print_header
                echo -e "  ${CYAN}▶ Checking backend folder...${NC}"
                echo ""
                if [ -d "backend/vendor" ]; then
                    echo -e "  ${GREEN}✓ vendor already exists — skipping install${NC}"
                    echo -e "  ${DIM}  Delete backend/vendor to reinstall${NC}"
                else
                    echo -e "  ${CYAN}▶ Installing Laravel...${NC}"
                    echo -e "  ${DIM}  CMD: composer create-project laravel/laravel backend${NC}"
                    echo ""
                    docker compose exec app composer create-project laravel/laravel backend --prefer-dist
                fi
                press_any_key
                ;;
            2)
                print_header
                echo -e "  ${CYAN}▶ Generating app key...${NC}"
                echo -e "  ${DIM}  CMD: php artisan key:generate${NC}"
                echo ""
                docker compose exec app bash -c "cd /var/www/html/backend && php artisan key:generate"
                press_any_key
                ;;
            3)
                print_header
                echo -e "  ${CYAN}▶ Running database migrations...${NC}"
                echo -e "  ${DIM}  CMD: php artisan migrate${NC}"
                echo ""
                docker compose exec app bash -c "cd /var/www/html/backend && php artisan migrate --force"
                press_any_key
                ;;
            4)
                print_header
                echo -e "  ${CYAN}▶ Generating sample CSV (10,000 rows)...${NC}"
                echo -e "  ${DIM}  CMD: php scripts/generate_csv.php${NC}"
                echo ""
                docker compose exec app php /var/www/html/backend/scripts/generate_csv.php
                press_any_key
                ;;
            5)
                print_header
                echo -e "  ${CYAN}▶ Ingesting CSV into Solr...${NC}"
                echo -e "  ${DIM}  CMD: php scripts/ingest_to_solr.php${NC}"
                echo ""
                docker compose exec app php /var/www/html/backend/scripts/ingest_to_solr.php
                press_any_key
                ;;
            6)
                print_header
                echo -e "  ${CYAN}▶ Running Kafka producer...${NC}"
                echo -e "  ${DIM}  CMD: php scripts/kafka_producer.php${NC}"
                echo ""
                docker compose exec app php /var/www/html/backend/scripts/kafka_producer.php
                press_any_key
                ;;
            7)
                print_header
                echo -e "  ${CYAN}▶ Clearing Laravel caches...${NC}"
                echo -e "  ${DIM}  CMD: php artisan config:clear && cache:clear && route:clear${NC}"
                echo ""
                docker compose exec app bash -c "cd /var/www/html/backend && php artisan config:clear && php artisan cache:clear && php artisan route:clear"
                press_any_key
                ;;
            8)
                print_header
                echo -e "  ${CYAN}▶ Checking Solr collection status...${NC}"
                echo -e "  ${DIM}  CMD: curl http://localhost:9007/solr/reports/select?q=*:*&rows=0${NC}"
                echo ""
                curl -s "http://localhost:9007/solr/reports/select?q=*:*&rows=0" | python3 -m json.tool 2>/dev/null || curl -s "http://localhost:9007/solr/reports/select?q=*:*&rows=0"
                press_any_key
                ;;
            0) break ;;
            *) echo -e "  ${RED}Invalid option.${NC}"; sleep 1 ;;
        esac
    done
}

# ─── FRONTEND MENU ─────────────────────────────────────
menu_frontend() {
    while true; do
        print_header
        echo -e "  ${WHITE}Frontend — React + Vite${NC}"
        echo ""

        if [ -d "frontend/node_modules" ]; then
            echo -e "  ${GREEN}✓ Node modules installed${NC}"
        else
            echo -e "  ${RED}✗ Node modules not found — run option [1]${NC}"
        fi

        echo ""
        echo -e "  ${GREEN}[1]${NC} Install dependencies (npm install)"
        echo -e "  ${GREEN}[2]${NC} Start dev server (npm run dev)"
        echo -e "  ${GREEN}[3]${NC} Build for production (npm run build)"
        print_divider
        echo -e "  ${YELLOW}[0]${NC} Back to main menu"
        echo ""
        echo -ne "  Choose an option [0-3]: "
        read choice

        case $choice in
            1)
                print_header
                echo -e "  ${CYAN}▶ Installing frontend dependencies...${NC}"
                echo -e "  ${DIM}  CMD: npm install${NC}"
                echo ""
                docker compose exec app bash -c "cd /var/www/html/frontend && npm install"
                press_any_key
                ;;
            2)
                print_header
                echo -e "  ${CYAN}▶ Starting Vite dev server...${NC}"
                echo -e "  ${DIM}  CMD: npm run dev${NC}"
                echo -e "  ${YELLOW}  Press Ctrl+C to stop the server${NC}"
                echo ""
                docker compose exec app bash -c "cd /var/www/html/frontend && npm run dev -- --host"
                press_any_key
                ;;
            3)
                print_header
                echo -e "  ${CYAN}▶ Building for production...${NC}"
                echo -e "  ${DIM}  CMD: npm run build${NC}"
                echo ""
                docker compose exec app bash -c "cd /var/www/html/frontend && npm run build"
                press_any_key
                ;;
            0) break ;;
            *) echo -e "  ${RED}Invalid option.${NC}"; sleep 1 ;;
        esac
    done
}

# ─── MAIN MENU ─────────────────────────────────────────
main_menu() {
    while true; do
        print_header
        echo -e "  ${WHITE}Main Menu${NC}"
        echo ""
        echo -e "  ${GREEN}[1]${NC} Docker    — Start / Stop / Rebuild containers"
        echo -e "  ${GREEN}[2]${NC} Backend   — Laravel install / migrate / ingest"
        echo -e "  ${GREEN}[3]${NC} Frontend  — React install / dev server / build"
        print_divider
        echo -e "  ${YELLOW}[0]${NC} Exit"
        echo ""
        echo -ne "  Choose an option [0-3]: "
        read choice

        case $choice in
            1) menu_docker ;;
            2) menu_backend ;;
            3) menu_frontend ;;
            0)
                echo ""
                echo -e "  ${CYAN}Goodbye!${NC}"
                echo ""
                exit 0
                ;;
            *) echo -e "  ${RED}Invalid option.${NC}"; sleep 1 ;;
        esac
    done
}

main_menu