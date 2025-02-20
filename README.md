Для инициализации пространства имен 
    composer install

Блок http://localhost работает на обработку запросов по верификации скобок
    curl -s -w "%{http_code}" -X POST -d "string=()" http://localhost

Тест балансировщика:
    curl http://localhost/test