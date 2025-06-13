<?

class FileStorage {

    public function __invoke($filename)
    {
        $redis = new \Redis();
        $redis->connect('redis', 6379);
        $result = $redis->del($filename);

        if ($result) {
            echo "Очередь № {$filename} успешно удалена.\n";
        } else {
            echo "Очередь № {$filename} не найдена или уже удалена.\n";
        }
    }

}