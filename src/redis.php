<? 

$redis = new Redis();
$redis->connect(getenv('REDIS_HOST'), 6379);
$redis->set("test_key", "Hello Redis!");
echo $redis->get("test_key") . "<br>";

 