<? 

require_once "vendor/autoload.php";

try {
    $myapp = new \MyTestApp\MyApp();
    echo $myapp->render;
}

catch(\Exception $e) {
    echo "Ошибка данных: ".$e->getMessage();
} 







 