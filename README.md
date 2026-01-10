### Использование

Не проходит:
```
curl -X POST -d 'string=(()()()()))((((()()()))(()()()(((()))))))' http://mysite.local
```
Проходит:
```
curl -X POST -d 'string=(()())' http://mysite.local
```

или через форму http://mysite.local/
