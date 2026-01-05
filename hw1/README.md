# PHP_2025

### Описание/Пошаговая инструкция выполнения задания:
1. Установить **Docker** себе на локальную машину.
2. Описать инфраструктуру в **docker-compose**, которая включает в себя:
- **nginx** (обрабатывает статику, пробрасывает выполнение скриптов в fpm).
- **php-fpm** (соединяется с `nginx` через **unix-сокет**).
- **redis** (соединяется с `php` по порту).
- **memcached** (соединяется с `php` по порту).
3. БД соединяется по порту (не забудьте про директории с данными)
4. Можно установить Composer

### Проверить что установлен Composer можно слдеующим способом

```
    docker exec -it app bash          
    www-data@603fc60654b6:/data/application.local$ composer --version
    Composer version 2.9.3 2025-12-30 13:40:17
    PHP version 8.1.34 (/usr/local/bin/php)
    Run the "diagnose" command to get more detailed diagnostics output.
```

5. Соединить FPM и Nginx через unix-сокет

## Документация по настройке Unix-сокета между PHP-FPM и Nginx

## 📋 Обзор архитектуры

┌─────────────┐      Unix Socket     ┌─────────────┐
│   Nginx     │◄────────────────────►│   PHP-FPM   │
│   контейнер │ /var/run/php/php-    │   контейнер │
│             │     fpm.sock         │             │
└─────────────┘                      └─────────────┘
       ▲                                    ▲
       │                                    │
       ▼                                    ▼
┌─────────────────────────────────────────────────┐
│           Общий Docker Volume: php_socket       │
└─────────────────────────────────────────────────┘

Ключевые конфигурационные файлы

## 1. PHP-FPM Конфигурация (docker/fpm/php-fpm.conf)

```
ini
[www]
user = www-data
group = www-data
```

### Ключевая настройка: путь к Unix-сокету
```
listen = /var/run/php/php-fpm.sock
```

### Права доступа к сокету
```
listen.owner = www-data
listen.group = www-data
listen.mode = 0660  # Режим доступа (0666 для отладки)
```

### Настройки пула процессов
```
pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
```

## 2. Dockerfile для PHP-FPM (docker/fpm/Dockerfile)

dockerfile
### Создание директории для сокета с правильными правами
```
RUN mkdir -p /var/run/php && \
    chown www-data:www-data /var/run/php && \
    chmod 755 /var/run/php
```

### Копирование конфигурации (ЗАМЕЩАЕТ стандартный конфиг)

```
COPY php-fpm.conf /usr/local/etc/php-fpm.conf
```

## 3. Конфигурация Nginx (docker/nginx/hosts/application.local.conf)

```
nginx
server {
    listen 80;
    server_name application.local;
    root /data/application.local;
    
    location ~ \.php$ {
        # Ключевая настройка: путь к Unix-сокету PHP-FPM
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        
        # Обязательные параметры
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        include fastcgi_params;
        
        # Оптимизация
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
        fastcgi_read_timeout 300;
    }
}
```
## 4. Docker Compose (docker-compose.yml)

```
yaml
services:
  php-fpm:
    volumes:
      # Общий volume для Unix-сокета
      - php_socket:/var/run/php
  
  nginx:
    volumes:
      # Тот же volume для доступа к сокету
      - php_socket:/var/run/php

volumes:
  # Определение общего volume для сокета
  php_socket:
```

### Виртуальные машины.
1.  Развернуть `Homestead VM` при помощи `Vagrant` и `VirtualBox`.
2.  Сайт должен отвечать на доменное имя **application.local**.
3.  Должна быть поддержка проброса директорий.
