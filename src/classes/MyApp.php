<? 

namespace MyTestApp;

Class MyApp {

    public function __construct($host,$dbName,$user,$pass) {

        try {

            $pdo = new \PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $user, $pass);

            $collection = new \MyTestApp\DataMapper\UserMapper($pdo);

            // Заносим данные в коллекцию identityMap

            $collection->findById(1);
            $collection->findById(3);

            new \MyTestApp\RenderHtml("views/page.php",[
                "identityMap1"=>$collection->identityMap, // Выводим коллекцию
                "identityMap2"=>$collection->findAll(0,1000) // Получаем все данные с учетом существующих в коллекции
            ]);
            
        } catch (\PDOException $e) {

            throw new \Exception("Connection failed: " . $e->getMessage());
            
        }
        
    }

}