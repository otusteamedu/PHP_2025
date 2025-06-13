<?

namespace src\Infrastructure\Api\V1;
use Src\Infrastructure\Queue\Producer\Producer;
use Src\Infrastructure\Api\V1\Common;

class EntrancePoint {

    public function __invoke() {

        switch (Common::get_method()) {

            case "POST":
                $api = new \Src\Infrastructure\Api\V1\Post\MethodPost(Common::get_endpoint("/Api/V1"),Common::get_request_data());
                $api->endpoint();
                break;
            
            case "GET":
                $api = new \Src\Infrastructure\Api\V1\Get\MethodGet(Common::get_endpoint("/Api/V1"),Common::get_request_data());
                $api->endpoint();
                break;

            default:
                Common::send_response(array(
                    'code' => 405,
                    'status' => 'failed',
                    'message' => 'Method not allowed'
                ), 405);
        }

    }

}