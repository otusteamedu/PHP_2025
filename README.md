# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

## Clean Architecture

Проект организован согласно принципам чистой архитектуры:

```
src/
├── Domain/                    # Доменный слой (ядро)
│   ├── Entity/               # Сущности
│   │   └── Product.php
│   └── Repository/           # Интерфейсы репозиториев
│       └── ProductRepositoryInterface.php
│
├── Application/              # Слой приложения
│   └── UseCase/              # Use Cases (бизнес-логика)
│       └── Product/
│           ├── CreateProductUseCase.php
│           ├── GetProductsUseCase.php
│           ├── UpdateProductUseCase.php
│           └── DeleteProductUseCase.php
│
├── Infrastructure/           # Инфраструктурный слой
│   ├── Bus/                 # Шина команд
│   │   └── Bus.php
│   ├── Config/              # Конфигурация
│   │   ├── ArrayReader.php
│   │   ├── Config.php
│   │   ├── ConfigInterface.php
│   │   └── ReaderInterface.php
│   ├── Database/            # Подключение к БД
│   │   └── DatabaseConnection.php
│   ├── Di/                  # Контейнер зависимостей
│   │   ├── Container.php
│   │   └── UnresolveParameterException.php
│   ├── Kernel/              # Ядро приложения
│   │   ├── AbstractKernel.php
│   │   └── Console.php
│   └── Persistence/         # Реализации репозиториев
│       ├── Mapper/
│       │   └── ProductMapper.php
│       └── Repository/
│           └── ProductRepository.php
│
└── Presentation/            # Слой представления
    └── Console/             # Консольные команды
        ├── InsertProductCommand.php
        ├── ListProductsCommand.php
        ├── UpdateProductCommand.php
        └── DeleteProductCommand.php
```

### Принципы:

1. **Domain** - не зависит от других слоёв, содержит бизнес-сущности и интерфейсы
2. **Application** - зависит только от Domain, содержит Use Cases
3. **Infrastructure** - реализует интерфейсы из Domain, работает с внешними системами
4. **Presentation** - точка входа, использует Application слой

### Dependency Injection

Все зависимости инжектируются через конструктор и настраиваются в `config/container.php`

# Configuring

```shell
cp .env.dist .env
```

```shell
cp html/.env.dist.php html/.env.php
```

# Build

```shell
docker compose build
```

# Composer

```shell
docker compose run --rm application composer install
```

# Up

```shell
docker compose up -d
```

# DDL

```shell
cat html/data/ddl.sql | docker compose exec --no-TTY postgres psql --host localhost --username otus
```

# Insert

```shell
docker compose exec application php bin/console.php insert
```

# List

```shell
docker compose exec application php bin/console.php list
```

# Update

```shell
docker compose exec application php bin/console.php update
```

# Delete

```shell
docker compose exec application php bin/console.php delete
```

# Down

```shell
docker compose down -v
```
