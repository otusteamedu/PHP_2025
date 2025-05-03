<?

//require_once "vendor/autoload.php";
spl_autoload_register();

session_start();

/* try {
    $name = new Domain\ValueObject\Url("https://ya.ru");
    echo $name->getValue();
} 

catch (\Exception $e) {
    echo 'Ошибка: ',  $e->getMessage(), "";
} */


/* $new = new Infrastructure\Factory\CommonNewsFactory;
$new->create("https://ya.ru");

var_dump($new);
echo "<hr/>";
$save_new = new Infrastructure\Repository\FileNewsRepository;
$mynew = new Domain\Entity\News(new Domain\ValueObject\Url("https://ya.ru"));
$save_new->save($mynew);

echo "<pre>";
var_dump($mynew);
echo "</pre>"; */

/* $req = new Application\UseCase\AddNews\SubmitNewsRequest("https://ya.ru");
 

$response = 
(
    (New Infrastructure\Http\SubmitNewsController(
        (new Application\UseCase\AddNews\SubmitNewsUseCase(
            new Infrastructure\Factory\CommonNewsFactory,
            new Infrastructure\Repository\FileNewsRepository
        ))
    ))($req)
);

echo "<pre>";
var_dump($response);
echo "</pre>"; */


 

 


 

 
// Работает

use Application\UseCase\AddNews\SubmitNewsUseCase;
use Infrastructure\Factory\CommonNewsFactory;
use Infrastructure\Repository\FileNewsRepository;

(
    new Infrastructure\Http\SubmitNewsController(
        (
            new SubmitNewsUseCase(
                new CommonNewsFactory,
                new FileNewsRepository
            )
        )
    )
)("https://ria.ru/20250503/griby-2014723317.html");





$redis = new \Redis();
$redis->connect(getenv('REDIS_HOST'), 6379);
//$redis->flushDB(); exit();

/* $keys = $redis->keys('*');
$count = count($keys);
// Сохраняем запись в Redis

$data = [
    "url"=>"www".$count,
    "title"=>"Заголовок",
    "date"=>"01.01.2025"
];

$redis->set($count++, json_encode($data));

print_r($keys);

echo "<br/>"; */


//echo $redis->get(session_id());

$allRecords = [];

$keys = $redis->keys('*');

print_r($keys);

foreach ($keys as $key) {
    $allRecords[$key] = $redis->get($key); // В зависимости от типа данных используйте соответствующие методы
}

ksort($allRecords);

// Выводим все записи
foreach ($allRecords as $key => $value) {
    echo "<p>Ключ: $key, Значение: $value</p>";
} 
/* echo "<hr/>";
echo $redis->get(0); */
 



 