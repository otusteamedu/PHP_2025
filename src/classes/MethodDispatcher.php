<? 

namespace MyTestApp;

Class MethodDispatcher {

    public function __construct($redis) {

        if(isset($_POST["string"])) { 
            
            // Если строка не валидна отдаем код 400 и записываем данные в Редис об ошибочной валидации

            if(!\MyTestApp\Validation::isValidBrackets($_POST["string"])) {
                http_response_code(400);
                $redis->set(session_id(), $redis->get(session_id()). "Запрос обработал контейнер: " . $_SERVER['HOSTNAME']." . Строка \"{$_POST["string"]}\" - ошибка<br/>");
            }

            // Если строка не валидна записываем данные в Редис о верной валидации
                
            else 
                $redis->set(session_id(), $redis->get(session_id()). "Запрос обработал контейнер: " . $_SERVER['HOSTNAME']." . Строка \"{$_POST["string"]}\" - верно<br/>");
            
                
        }

        

    }

}