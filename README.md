Создание задачи:

curl -X POST http://localhost:8080/api/jobs \
  -H "Content-Type: application/json" \
  -d '{"data": {"items": ["test1", "test2"]}}'

Проверка статуса:

curl http://localhost:8080/api/jobs/job_65a8f1b2c3d4e

Документация: http://localhost:8081
