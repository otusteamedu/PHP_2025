<? 

namespace MyTestApp;

Class RedisConnect {

    public $redis_connect;

    public function __construct() {
        $redis = new \Redis();
        $redis->connect(getenv('REDIS_HOST'), 6379);
        $this->redis_connect = $redis;
    }

}