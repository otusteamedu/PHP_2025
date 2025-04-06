<? 

require_once "vendor/autoload.php";

try {
    $myapp = new \MyTestApp\MyApp(
        getenv('MYSQL_HOST'),
        getenv('MYSQL_DATABASE'),
        'root',
        's123123'
    );
    echo $myapp->render;
}

catch(\Exception $e) {
    echo "Ошибка данных: ".$e->getMessage();
} 







 