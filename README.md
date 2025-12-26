# HW11

**Redis + MongoDB**

Примеры запросов событий:
```bash
curl -X POST http://app.local/events \
-H "Content-Type: application/json" \
-d '{"priority":1000,"conditions":{"param1":1},"event":{"::event::":1}}'


curl -X POST http://app.local/events \
-H "Content-Type: application/json" \
-d '{"priority":2000,"conditions":{"param1":2,"param2":2},"event":{"::event::":2}}'


curl -X POST http://app.local/events \
-H "Content-Type: application/json" \
-d '{"priority":3000,"conditions":{"param1":1,"param2":2},"event":{"::event::":3}}'
```


Пример запроса для ответа пользователю наиболее подходящим событием:
```bash
curl -X POST http://app.local/match \
-H "Content-Type: application/json" \
-d '{"params":{"param1":1,"param2":2}}'
```


Удалить все события:
```bash
curl -X DELETE http://app.local/events
```


