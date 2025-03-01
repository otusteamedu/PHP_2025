### Примеры запросов

Успешный:
```
curl -X POST -d 'string=()' http://localhost
```

Неудачный:
```
curl -X POST -d 'string=(()()()()))((((()()()))(()()()(((()))))))' http://localhost
```