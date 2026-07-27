# Email validator

Перед запуском приложения нужно выполнить команды:

- Перейти в папку app (`cd /app/`);
- Запускаем `sudo docker compose up -d`;
- Перейти в папку  (`cd /app/deploy/`);
- Запускаем `sudo docker compose up gateway -d`;
- Запускам **только сборку** всех остальных образов `sudo docker compose build`;

Приложение запускается на виртуальной машине:

- Перейти в родительскую директорию (`cd /app/`);
- Запустить скрипт командой `sudo sh run.sh test@test.ru`.
