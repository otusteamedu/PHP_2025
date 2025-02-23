<? 

session_start();

require_once "vendor/autoload.php";

$redis = new Redis();
$redis->connect(getenv('REDIS_HOST'), 6379);

$renderHtml = new \MyTestApp\RenderHtml();

if(isset($_POST["string"])) { 
    
    // Если строка не валидна отдаем код 400 и записываем данные в Редис об ошибочной валидации

    if(!\MyTestApp\Common::isValidBrackets($_POST["string"])) {
        http_response_code(400);
        $redis->set(session_id(), $redis->get(session_id()). "Запрос обработал контейнер: " . $_SERVER['HOSTNAME']." . Строка \"{$_POST["string"]}\" - ошибка<br/>");
    }

    // Если строка не валидна записываем данные в Редис о верной валидации
        
    else 
        $redis->set(session_id(), $redis->get(session_id()). "Запрос обработал контейнер: " . $_SERVER['HOSTNAME']." . Строка \"{$_POST["string"]}\" - верно<br/>");
    
        
}

// Если нет данных с ключем сессии, то создаем запись с этим ключем

if(!$redis->get(session_id()))
    $redis->set(session_id(), "Запрос обработал контейнер: " . $_SERVER['HOSTNAME'].". Сессия записана в Redis. Ключ: ".session_id()."</br>");

// Выводим данные по ключу сессии (для кластера)

else 
    $renderHtml->renderHtml($redis->get(session_id()));


$renderHtml->renderHtml("<hr/>");
$renderHtml->renderHtml("
<form method='post'>
    <input type='text' size='50' name='string' value='' placeholder='Введите строку для проверки' />
    <input type='submit' value='Проверить'/>
</form>
");

echo $renderHtml->html;




 