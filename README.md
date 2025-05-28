# PHP_2025

## Описание выполненного ДЗ №4
В рамках домашнего задания разработан балансируемый кластер Nginx, трех серверов PHP-FPM
и Redis Cluster из трёх серверов.

### Краткое описание архитектуры:
```mermaid
flowchart LR
    subgraph Balancer
        A["Nginx-Balancer (80)"]
    end
    subgraph Backend
        A -->|fastcgi_pass|B("PhpFpm_S1 (9000)")
        A -->|fastcgi_pass|C("PhpFpm_S2 (9000)")
        A -->|fastcgi_pass|D("PhpFpm_S3 (9000)")
    end
    subgraph Redis Cluster
        E["Redis_S1 (6379)"]
        F["Redis_S2 (6379)"]
        G["Redis_S3 (6379)"]
        B & C & D -->|session| E & F & G
    end
```
### Запуск
Установить зависимости через Composer:
```bash
composer install
```

Для запуска контейнеров:
1. Перейти в каталог docker
```bash
cd docker
```
2. Запустить контейнеры
```bash
docker-compose up -d
```

Инициализируйте Redis Cluster:
```bash
docker exec -it Redis_S1 redis-cli --cluster create Redis_S1:6379 Redis_S2:6379 Redis_S3:6379 --cluster-replicas 0
```