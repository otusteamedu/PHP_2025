<?

//require_once "vendor/autoload.php";
spl_autoload_register();

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
)("https://ya.ru");






 