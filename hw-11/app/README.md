Получение событий
php ./app/public/index.php --action=get --conditions=param1=2,param2=2

Добавление событий
php ./app/public/index.php --action=create --priority=1000 --conditions=param1=1
php ./app/public/index.php --action=create --priority=2000 --conditions=param1=2,param2=2
php ./app/public/index.php --action=create --priority=3000 --conditions=param1=1,param2=2

Удаление событий
php ./app/public/index.php --action=delete --priority=3000 --conditions=param1=2,param2=2
