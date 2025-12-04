<?php

namespace App;

require_once __DIR__ . '/Auth/Auth.php';
require_once __DIR__ . '/Response/Response.php';
require_once __DIR__ . '/Service/Validator.php';

use Auth\Auth;
use Response\Response;
use Service\Validator;

class App {

	public function run(){
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$auth = new Auth();
			$auth->sessionStart();
			$validator = new Validator();
			$validator->validate();
			$response = new Response();
			return $response->sendReply($validator->getMessage(), $validator->getCode()); 
		}
	}
}