<? 

namespace MyTestApp;

Class MyApp {

    public $render;

    public function __construct($host,$dbName,$user,$pass) {

        try {

            $pdo = new \PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $user, $pass);

            $collection = new \MyTestApp\DataMapper\UserMapper($pdo);
            $collection->findById(1);
            $collection->findById(3);
            $this->render .= "<h2>Заносим данные в коллекцию identityMap</h2>";
            $this->render .= "<p>\$collection->findById(1)</p>";
            $this->render .= "<p>\$collection->findById(2)</p>";

            $this->render .= "<h2>Получаем данные из identityMap</h2>";
            foreach($collection->identityMap AS $user) {
                $this->render .= "<p>{$user->getId()} {$user->getName()} {$user->getEmail()}</p>";
            }

            $this->render .= "<h2>Получаем данные из базы и дописываем те, которых нет, в identityMap</h2>";
            $users_array = ($collection)->findAll();
            foreach($users_array AS $user) {
                $this->render .= "<p>{$user->getId()} {$user->getName()} {$user->getEmail()}</p>";
            } 
            
        } catch (\PDOException $e) {

            throw new \Exception("Connection failed: " . $e->getMessage());
            
        }
        
    }

}