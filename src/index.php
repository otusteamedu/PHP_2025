<? 

require_once "vendor/autoload.php";

try {
    new \MyTestApp\MyApp(
        getenv('MYSQL_HOST'),
        getenv('MYSQL_DATABASE'),
        'root',
        's123123'
    );
}

catch(\Exception $e) {
    echo "Ошибка данных: ".$e->getMessage();
} 







 