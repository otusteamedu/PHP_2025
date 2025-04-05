<? 

namespace MyTestApp;

Class MyApp {

    public $render;

    public function __construct() {

        $host = getenv('MYSQL_HOST'); // use service name defined in docker-compose
        $dbName = getenv('MYSQL_DATABASE');
        $user = 'root';
        $pass = 's123123';

        try {

            $dsn = "mysql:host=$host;dbname=$dbName;charset=utf8mb4";
            $pdo = new \PDO($dsn, $user, $pass);


            $collection = new \MyTestApp\DataMapper\UserMapper($pdo);
            $collection->findById(1);
            $collection->findById(3);
            foreach($collection->identityMap AS $user) {
                $this->render .= "<p>{$user->getId()} {$user->getName()} {$user->getEmail()}</p>";
            }
           // $this->render .= "<p>".print_r($collection->identityMap)."</p>";
            
            //$user_1 = new \MyTestApp\DataMapper\User(1,"Артем","artemsuchkov@ya.ru");
            //$user_update = (new \MyTestApp\DataMapper\UserMapper($pdo))->update($user_1);
            //if($user_update)
            //    $this->render .= "<p>Данные обновлены</p>";


            $users_array = (new \MyTestApp\DataMapper\UserMapper($pdo))->findAll();
            foreach($users_array AS $user) {
                $this->render .= "<p>{$user->getId()} {$user->getName()} {$user->getEmail()}</p>";
            } 
            

        } catch (\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        
    }

}