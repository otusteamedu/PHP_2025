<?php


namespace Infrastructure\Rest;

class Common {

	public static function get_endpoint () {
		return (preg_split("/\//", $_SERVER["REQUEST_URI"], -1, PREG_SPLIT_NO_EMPTY));
	}

	public static function get_method () {
		return $_SERVER['REQUEST_METHOD'];
	}

	public static function get_request_data () {
		return array_merge(empty($_POST) ? array() : $_POST, (array) json_decode(file_get_contents('php://input'), true), $_GET);
	}

	public static function send_response ($response, $code = 200) {
		http_response_code($code);
		die(json_encode($response, JSON_UNESCAPED_UNICODE));
	}

	public static function save_html (array $array,string $folder) {
		$html = "";
		foreach($array AS $data) {
			$html .= "<ul><li><a href='{$data["url"]}'>{$data["title"]}</a></li></ul>";
		}
		$file_name = self::generateRandomString().".html";
		if(file_put_contents($_SERVER['DOCUMENT_ROOT'].$folder.$file_name, "\xEF\xBB\xBF".$html)) {
			return $folder.$file_name; 
		} else {
			return false;
		}
	}

	public static function generateRandomString($length = 12) {
		
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
	
		for ($i = 0; $i < $length; $i++) {
			$randomIndex = random_int(0, $charactersLength - 1); 
			$randomString .= $characters[$randomIndex];
		}
	
		return $randomString;
	}
 
}