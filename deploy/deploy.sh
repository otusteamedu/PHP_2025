#!/bin/bash

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

ENVIRONMENT="production"
CONFIG_REPO=""
CONFIG_BRANCH="main"
DOCKER_COMPOSE_OPTS=""
NO_CACHE=false
ROLLBACK=false

usage() {
    echo "Usage: $0 [OPTIONS]"
    echo "Options:"
    echo "  -e, --environment ENV    Environment (production|staging) [default: production]"
    echo "  -c, --config-repo URL    Private config repository URL"
    echo "  -b, --branch BRANCH      Config repository branch [default: main]"
    echo "  -n, --no-cache           Build without cache"
    echo "  -r, --rollback           Rollback to previous version"
    echo "  -h, --help               Show this help"
    exit 1
}

# Парсинг аргументов
while [[ $# -gt 0 ]]; do
    case $1 in
        -e|--environment)
            ENVIRONMENT="$2"
            shift 2
            ;;
        -c|--config-repo)
            CONFIG_REPO="$2"
            shift 2
            ;;
        -b|--branch)
            CONFIG_BRANCH="$2"
            shift 2
            ;;
        -n|--no-cache)
            NO_CACHE=true
            shift
            ;;
        -r|--rollback)
            ROLLBACK=true
            shift
            ;;
        -h|--help)
            usage
            ;;
        *)
            echo "Unknown option: $1"
            usage
            ;;
    esac
done

log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

warn() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARNING: $1${NC}"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $1${NC}"
    exit 1
}

# Функция для проверки зависимостей
check_dependencies() {
    log "Checking dependencies..."
    
    command -v docker >/dev/null 2>&1 || error "Docker is not installed"
    command -v docker-compose >/dev/null 2>&1 || error "Docker Compose is not installed"
    command -v git >/dev/null 2>&1 || error "Git is not installed"
    
    log "All dependencies are satisfied"
}

# Функция для загрузки конфигураций из приватного репозитория
fetch_configs() {
    if [[ -z "$CONFIG_REPO" ]]; then
        warn "No config repository specified, using local configs"
        return 0
    fi
    
    log "Fetching configuration from $CONFIG_REPO (branch: $CONFIG_BRANCH)"
    
    local CONFIG_DIR="./configs/$ENVIRONMENT"
    mkdir -p "$CONFIG_DIR"
    
    if [[ -d "$CONFIG_DIR/.git" ]]; then
        cd "$CONFIG_DIR"
        git pull origin "$CONFIG_BRANCH"
        cd - > /dev/null
    else
        git clone -b "$CONFIG_BRANCH" "$CONFIG_REPO" "$CONFIG_DIR"
    fi
    
    if [[ -f "$CONFIG_DIR/.env.$ENVIRONMENT" ]]; then
        cp "$CONFIG_DIR/.env.$ENVIRONMENT" "./.env.$ENVIRONMENT"
        log "Environment file copied"
    fi
    
    if [[ -f "$CONFIG_DIR/nginx.conf" ]]; then
        cp "$CONFIG_DIR/nginx.conf" "./deploy/nginx/default.conf"
        log "Nginx config copied"
    fi
    
    log "Configuration files updated"
}

# Функция для настройки среды
setup_environment() {
    log "Setting up $ENVIRONMENT environment"
    
    if [[ ! -f ".env.$ENVIRONMENT" ]]; then
        error "Environment file .env.$ENVIRONMENT not found"
    fi
    
    cp ".env.$ENVIRONMENT" ".env"
    source ".env"
    
    log "Environment $ENVIRONMENT configured"
}

# Функция для сборки образов
build_images() {
    log "Building Docker images..."
    
    local BUILD_ARGS=""
    if [[ "$NO_CACHE" == true ]]; then
        BUILD_ARGS="--no-cache"
        log "Building without cache"
    fi
    
    docker-compose build $BUILD_ARGS || error "Failed to build images"
    log "Images built successfully"
}

# Функция для деплоя без downtime
deploy_zero_downtime() {
    log "Starting zero-downtime deployment..."

    docker-compose up -d --scale app=2 --no-recreate

    log "Waiting for new containers to become healthy..."
    sleep 30

    docker-compose up -d --no-deps app

    docker-compose up -d --scale app=1
    
    log "Zero-downtime deployment completed"
}

# Функция для отката
rollback_deployment() {
    log "Starting rollback..."

    docker-compose pull app
    docker-compose up -d app
    
    log "Rollback completed"
}

# Функция для очистки
cleanup() {
    log "Cleaning up..."

    docker image prune -f

    docker container prune -f
    
    log "Cleanup completed"
}

# Функция для проверки здоровья
health_check() {
    log "Performing health check..."
    
    local MAX_RETRIES=30
    local RETRY_COUNT=0
    
    while [[ $RETRY_COUNT -lt $MAX_RETRIES ]]; do
        if curl -f http://localhost:${NGINX_PORT:-80}/health >/dev/null 2>&1; then
            log "Health check passed"
            return 0
        fi
        
        RETRY_COUNT=$((RETRY_COUNT + 1))
        warn "Health check failed, retrying... ($RETRY_COUNT/$MAX_RETRIES)"
        sleep 10
    done
    
    error "Health check failed after $MAX_RETRIES attempts"
}

# Основная функция
main() {
    log "Starting deployment process for $ENVIRONMENT environment"
    
    check_dependencies
    fetch_configs
    setup_environment
    
    if [[ "$ROLLBACK" == true ]]; then
        rollback_deployment
    else
        build_images
        deploy_zero_downtime
    fi
    
    health_check
    cleanup
    
    log "Deployment completed successfully!"
}

main "$@"