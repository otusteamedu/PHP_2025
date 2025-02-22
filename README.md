# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

## 3. Подключение кластера Redis
тут указано, что не вошло в код дз. брал отсюда https://ilhamdcp.hashnode.dev/creating-redis-cluster-with-docker-and-compose

в коде подключение так, видимо, в конфигах надо пробрасывать, как обычные настройки, минимальное количество - 6 контейнеров
```shell
  $client = new \Predis\Client(
   [
      ["host" => "redis-node-1", "port" => 5381],
      ["host" => "redis-node-2", "port" => 5382],
      ["host" => "redis-node-3", "port" => 5383],
      ["host" => "redis-node-4", "port" => 5384],
      ["host" => "redis-node-5", "port" => 5385],
      ["host" => "redis-node-6", "port" => 5386]
  ],
   ['cluster' => 'redis']
);
   ```
для проверки работы сохранения сессий
```shell
session_start();
$count = isset($_SESSION['count']) ? $_SESSION['count'] : 1;
$_SESSION['count'] = ++$count;
echo $count;
   ```



