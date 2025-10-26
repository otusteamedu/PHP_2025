<?php
echo "Hello by ATelepchenkov<br/>";
try {
    $pdo = new PDO('pgsql:host=postgres;port=5432;dbname=' . getenv('POSTGRES_DB'), getenv('POSTGRES_USER'), getenv('POSTGRES_PASSWORD'));

    $select = $pdo->query("SELECT 1 as n")->fetch();
    if($select['n'] == 1) {
        echo "Postgres connected successfully<br/>";
    } else {
        throw new Exception('Error connecting to SQL Server');
    }
} catch (PDOException $e) {
    echo "Postgres Error: " . $e->getMessage() . "<br/>";
} catch (Exception $e) {
    echo "Postgres data Error: " . $e->getMessage() . "<br/>";

}
//echo '<br/>ok';
$date = date('Y-m-d H:i:s');


try
{
    $redis = new Redis();
    $redis->connect('redis', 6379);

    $redis->hSet('test', 'date', $date);
    $rdata=$redis->hGetAll('test');
    if($rdata['date'] == $date) {
        echo "Redis connected successfully<br/>";
    } else {
        throw new Exception('Redis data established');
    }

}
catch (RedisException $e)
{
    echo 'Error connection Redis: ' . $e->getMessage() . '<br/>';
}
catch (Exception $e)
{
    echo 'Redis Data Error : ' . $e->getMessage() . '<br/>';
}

try
{
    $memcached = new Memcached();
    $memcached->addServer('memcached', 11211);

    $memcached->set('date', $date, 10);
    $memdata = $memcached->get('date');
    if($memdata == $date) {
        echo "Memcached connected successfully<br/>";
    } else {
        throw new Exception('Memcached data established');
    }
}
catch (Exception $e)
{
    echo 'Ошибка подключения к Memcached: ' . $e->getMessage() . '<br/>';
}