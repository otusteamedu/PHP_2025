<?

namespace src\Infrastructure\Api\V1;
use Src\Infrastructure\Queue\Producer\Producer;
use Src\Infrastructure\Api\V1\Common;

class EntrancePoint {

    public function __invoke() {

        switch (Common::get_method()) {

            case "POST":
                $api = new \Src\Infrastructure\Api\V1\Post\MethodPost(Common::get_endpoint(),Common::get_request_data());
                $api->endpoint();
                break;
            
            case "GET":
                $api = new \Src\Infrastructure\Api\V1\Get\MethodGet(Common::get_endpoint(),Common::get_request_data());
                $api->endpoint();
                break;

            default:
                Common::send_response(array(
                    'code' => 405,
                    'status' => 'failed',
                    'message' => 'Method not allowed'
                ), 405);
        }

        /* if(isset($_POST["date"]) AND isset($_POST["email"])) {
            $data["id"] = uniqid();
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
        </form>"; */



    }

}