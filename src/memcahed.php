<? 

$memcached = new Memcached();
$memcached->addServer(getenv('MEMCACHED_HOST'), 11211);
$memcached->set("test_key", "Hello Memcached!");
echo $memcached->get("test_key");
 