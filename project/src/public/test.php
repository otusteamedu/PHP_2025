<?php
echo "Hello by ATelepchenkov<br/>";
echo "PHP Server: " . getenv('SERVER') . "<br/>";
echo "NGinx Server " . $_SERVER['NGINX_SERVER'] . "<br/>";
echo "SESSION status:" . session_status() . "<br/>";
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}
echo "SESSION status:" . session_status() . "<br/>";
echo "SESSION id: " . session_id() . "<br/>";

$date = date('Y-m-d H:i:s');


try
{
    $redisServer = 'redis1';
    if(!$_SESSION['test1']) {
        $_SESSION['test1'] = 'set from ' . $_SERVER['SERVER'];
    }
    $redis = new RedisCluster(null, [
        'redis1:6379',
        'redis2:6379',
        'redis3:6379',
        'redis4:6379',
        'redis5:6379',
        'redis6:6379',
    ]);
//    $redis->connect($redisServer, 6379);
    $rdata = $redis->keys('*');
    echo '<pre>';
    print_r($rdata);
    print_r($redis->get($rdata[0]));
    echo '</pre>';
    echo "Redis connected successfully<br/>";

}
catch (RedisException $e)
{
    echo 'Error connection Redis: ' . $e->getMessage() . '<br/>';
}
catch (Exception $e)
{
    echo 'Redis Data Error : ' . $e->getMessage() . '<br/>';
}

//print_r($_SERVER);