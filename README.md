# Формирование отчета

1. Запусти приложение локально docker compose up -d
2. Отправь POST запрос http://localhost:8080/app/index.php
   {
   "user_id": 1,
   "interval": 2,
   "card_id": 91,
   "email": "test@email.com"
   }
 Сообщение попадет в очередь
3. Запусти воркер php Worker/ReportWorker.php