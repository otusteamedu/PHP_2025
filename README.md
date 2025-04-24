# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

## Парсер новостей (Приложение на Симфони, ДЗ 3-2)

## 1. Приложение умеет

* Создавать новость
* Отображать список
* Создавать файл отчета
* Отдавать файл с отчетом

## 2. Используемые технологии

Основные технологии: PHP, PostgreSQL

### 2.1 Общие технологии

| Технология | Версия | Описание       | Ссылка                     |
|------------|--------|----------------|----------------------------|
| PHP        | 8.4    | PHP            | https://www.php.net        |
| PostgreSQL | 17.4   | Реляционная БД | https://www.postgresql.org |
| Nginx      | 1.25.4 | Прокси-сервер  | https://www.nginx.com      |

## 4. Подготовка окружения для запуска на локальной машине

1. Проверить, что установлен Git
    ```shell
    git -v
    ```
2. Установить [Docker-compose](https://docs.docker.com/compose/install/linux/#install-the-plugin-manually).

3. Проверить, что установлен composer
   ```shell
   composer
   ```
   если нет установить [composer](https://getcomposer.org/download/).

## 4. Установка

1. Склонировать репозиторий в текущую директорию
    ```shell
    git clone git@github.com:otusteamedu/PHP_2025.git
    ```
2. Создать файлы .env путем их копирования из .env.example в директориях docker и app, установить значение переменных
    ```shell
    cd ./docker
    ```
3. Перейти в директорию с файлом docker-compose.yaml
    ```shell
    cd ./docker
    ```

## 5. Запуск

   ```shell
   docker compose up -d
   ```
