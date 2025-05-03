<?

//require_once "vendor/autoload.php";
spl_autoload_register();

use Infrastructure\Rest\Common;

switch (Common::get_method()) {

    case "POST":
        $api = new Infrastructure\Rest\Post\MethodPost(Common::get_endpoint(),Common::get_request_data());
		$api->endpoint();
        break;
    
    case "GET":
        $api = new Infrastructure\Rest\Get\MethodGet(Common::get_endpoint(),Common::get_request_data());
        $api->endpoint();
        break;

	default:
		Common::send_response(array(
			'code' => 405,
			'status' => 'failed',
			'message' => 'Method not allowed'
		), 405);
}




 



 