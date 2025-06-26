### Отправка и прослушка тестовых сообщений из очереди RabbitMQ

Для того, чтобы протестировать проект необходимо произвести следующие действия:
1) В папках ./Producer, ./, ./Consumer есть свой .env.example, его необходимо скопировать .env назвать
2) Произвести docker compose build
3) docker compose up -d Запускаем проект
4) docker compose exec producer bash В контейнере producer произвести composer install
5) Отправить POST запрос с полями bik, account, client, bank
6) Посмотреть обработку сообщения в консоли Consumer, посмотреть отправку сообщения в админке maildev
   
   
   