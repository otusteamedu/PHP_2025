Для инициализации пространства имен 
    composer install

Блок http://localhost работает на обработку запросов по верификации скобок
    curl -X POST http://localhost/check -d "{\"string\": \"((()\"}"

Тест балансировщика:
    curl http://localhost/test