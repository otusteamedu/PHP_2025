<?php
echo "Hello by ATelepchenkov<br/>";
echo "PHP Server: " . getenv('SERVER') . "<br/>";
echo "NGinx Server " . $_SERVER['NGINX_SERVER'] . "<br/>";
echo "SESSION status:" . session_status() . "<br/>";

try {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
} catch (Exception $e) {
    echo 'Session error: ' . $e->getMessage() . '<br/>';
}
echo "SESSION status:" . session_status() . "<br/>";
echo "SESSION id: " . session_id() . "<br/>";
if(!isset($_SESSION['view']['from'][$_SERVER['SERVER']]))
    $_SESSION['view']['from'][$_SERVER['SERVER']]=0;

$_SESSION['view']['from'][$_SERVER['SERVER']]++;

$date = date('Y-m-d H:i:s');


try
{
    if(!($_SESSION['test1']??false)) {
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
    if(isset($rdata[0]))
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