# Пример использования
## 1. Установить зависимости:
```shell
 composer install
```
## 2. Выполнить в терминале команду по конвертации DTO в SQL
### Для MySQL
```shell
 php ./vendor/bin/schemasync generate Dbykov\\OtusHw14\\Dto\\Car --dialect=mysql
```

### Для PostgreSQL
```shell
 php ./vendor/bin/schemasync generate Dbykov\\OtusHw14\\Dto\\Car --dialect=pgsql
```
