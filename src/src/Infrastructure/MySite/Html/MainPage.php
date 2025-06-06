<?

namespace src\Infrastructure\MySite\Html;
use Src\Infrastructure\Queue\Producer\Producer;

class MainPage {

    public function __invoke() {

        if(isset($_POST["date"]) AND isset($_POST["email"])) {
            $data["date"] = $_POST["date"]; 
            $data["email"] = $_POST["email"]; 
            (new Producer)(json_encode($data));
        }

        echo "
        <hr/>
        <form method='post'>
            <h2>Получить выгрузку</h2>
            <p>Выберите дату</p>
            <p><input type='date' name='date' value='' required /></p>
            <p>Впишите ваш эл. адрес</p>
            <p><input type='email' name='email' required /></p>
            <button type='submit'>Отправить</button>
        </form>";

    }

}