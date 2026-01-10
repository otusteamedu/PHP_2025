<br><b>Проверка соединения с PostgreSQL (PDO):</b>
<?
$dsn = 'pgsql:host=postgres;dbname=test';
$username = 'pguser';
$password = 'secret';

try
{
    $pdo = new PDO($dsn, $username, $password);
    echo '<p style="color:green;">Подключение к PostgreSQL успешно!</p>';
}
catch (PDOException $e)
{
    die('<p style="color:red;">Ошибка подключения к PostgreSQL: ' . $e->getMessage() . '</p>');
}
?>

<br><b>Проверка соединения с Redis (phpredis):</b>
<?
$redis = new Redis();

try
{
    $redis->connect('redis', 6379);
    echo '<p style="color:green;">Подключение к Redis успешно!</p>';
}
catch (RedisException $e)
{
    die('<p style="color:red;">Ошибка подключения к Redis: ' . $e->getMessage() . '</p>');
}
?>

<br><b>Проверка соединения с Memcached (memcache):</b>
<?
$server = 'memcached';
$port = 11211;
$memcache = new Memcache();

try
{
    $memcache->connect($server, $port);

    echo '<p style="color:green;">Подключение к Memcached успешно!</p>';
    print_r($memcache->getStats());
}
catch (Exception $e)
{
    die('<p style="color:red;">Ошибка подключения к Memcached: ' . $ex->getMessage() . '</p>');
}
?>

<br><br>
<b>Версия Composer: </b>
<? echo exec("composer -V"); ?>

<br><br>
<? phpinfo(); ?>