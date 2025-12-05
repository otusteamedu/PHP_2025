<?php

namespace App;

use App\Auth\Auth;
use App\Response\Response;
use App\Service\Validator;

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