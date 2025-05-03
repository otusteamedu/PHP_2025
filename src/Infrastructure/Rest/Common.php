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
 
}