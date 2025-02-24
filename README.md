Для инициализации пространства имен 
    composer install

Блок обработки emails на корректность
    curl -X POST http://localhost/check-emails -H "Content-Type: application/json" -d "{\"emails\": [\"user@example.com\", \"invalid-email\", \"test@fake-domain.xyz\"]}"

Блок http://localhost работает на обработку запросов по верификации скобок
    curl -X POST http://localhost/check -d "{\"string\": \"((()\"}"

Тест балансировщика:
    curl http://localhost/test