<?

namespace src\Infrastructure\Storage\FileStorage;
use src\Infrastructure\Storage\Storage;

class FileStorage implements Storage {

    private $redis;

    public function __construct() 
    {
        $redis = new \Redis();
        $redis->connect('redis', 6379);
        $this->redis = $redis;
    }

    public function save($json) 
    {

        $file_arr = json_decode($json, true);
        $this->redis->set($file_arr["id"], $json);
        return $file_arr["id"];

    }


}