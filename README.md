### Описание выполненного домашнего задания №11

Система управления событиями с поддержкой Redis и Tarantool хранилищ

#### Запуск

```bash
docker-compose build && docker-compose up -d
```

#### Использование


#### 1. Добавить новое событие в систему хранения событий
```bash
# Redis
# Событие 1: priority=1000, param1=1
curl -X POST "http://localhost:8080/api.php?action=add&storage=redis" \
  -H "Content-Type: application/json" \
  -d '{"priority": 1000, "conditions": {"param1": "1"}, "event": {"::event::": "first"}}'

# Событие 2: priority=2000, param1=2, param2=2  
curl -X POST "http://localhost:8080/api.php?action=add&storage=redis" \
  -H "Content-Type: application/json" \
  -d '{"priority": 2000, "conditions": {"param1": "2", "param2": "2"}, "event": {"::event::": "second"}}'

# Событие 3: priority=3000, param1=1, param2=2
curl -X POST "http://localhost:8080/api.php?action=add&storage=redis" \
  -H "Content-Type: application/json" \
  -d '{"priority": 3000, "conditions": {"param1": "1", "param2": "2"}, "event": {"::event::": "third"}}'

# Tarantool
# Событие 1: priority=1000, param1=1
curl -X POST "http://localhost:8080/api.php?action=add&storage=tarantool" \
  -H "Content-Type: application/json" \
  -d '{"priority": 1000, "conditions": {"param1": "1"}, "event": {"::event::": "first"}}'

# Событие 2: priority=2000, param1=2, param2=2  
curl -X POST "http://localhost:8080/api.php?action=add&storage=tarantool" \
  -H "Content-Type: application/json" \
  -d '{"priority": 2000, "conditions": {"param1": "2", "param2": "2"}, "event": {"::event::": "second"}}'

# Событие 3: priority=3000, param1=1, param2=2
curl -X POST "http://localhost:8080/api.php?action=add&storage=tarantool" \
  -H "Content-Type: application/json" \
  -d '{"priority": 3000, "conditions": {"param1": "1", "param2": "2"}, "event": {"::event::": "third"}}'
```

#### 2. Очистить все доступные события
```bash
# Redis
curl -X POST "http://localhost:8080/api.php?action=clear&storage=redis"

# Tarantool
curl -X POST "http://localhost:8080/api.php?action=clear&storage=tarantool"
```

#### 3. Ответить на запрос пользователя наиболее подходящим событием
```bash
# Redis
# Запрос: param1=1, param2=2 (подходят события 1 и 3, выбирается с priority=3000)
curl -X POST "http://localhost:8080/api.php?action=find&storage=redis" \
  -H "Content-Type: application/json" \
  -d '{"param1": "1", "param2": "2"}'

# Анлогично для Tarantool  
curl -X POST "http://localhost:8080/api.php?action=find&storage=tarantool" \
  -H "Content-Type: application/json" \
  -d '{"param1": "1", "param2": "2"}'
```


