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

/* $req = new Application\UseCase\AddNews\SubmitNewsRequest("https1://ya.ru");
 

$response = 
(
    (New Infrastructure\Http\SubmitNewsController(
        (new Application\UseCase\AddNews\SubmitNewsUseCase(
            new Infrastructure\Factory\CommonNewsFactory($req),
            new Infrastructure\Repository\FileNewsRepository($req)
        ))
    ))($req)
);

echo "<pre>";
var_dump($response);
echo "</pre>"; */

use Application\UseCase\AddNews\SubmitNewsUseCase;
use Application\UseCase\AddNews\SubmitNewsRequest;

class SubmitLeadCommand 
{
    public function __construct(
        private SubmitNewsUseCase $useCase,
    )
    {

    }

    public function show($url): void
    {
        try {
            $submitLeadRequest = new SubmitNewsRequest($url);
            $submitLeadResponse = ($this->useCase)($submitLeadRequest);
            echo 'Lead ID: ' . $submitLeadResponse->id;
        } catch (\Throwable $e) {
            echo $e->getMessage();
        }
    }
}

$res = new SubmitLeadCommand((new Application\UseCase\AddNews\SubmitNewsUseCase(
    new Infrastructure\Factory\CommonNewsFactory,
    new Infrastructure\Repository\FileNewsRepository
)));

$res->show("https://ya.ru");

//phpinfo();




 