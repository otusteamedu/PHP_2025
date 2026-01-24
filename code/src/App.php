<?php

namespace App;

use App\Response\Response;
use App\Service\Validator;

class App {

	public function run(){
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$string = $_POST['string'] ? $_POST['string'] : '';
			$validator = new Validator();
			$result = $validator->validate($string);
			$response = new Response();
   			return $response->send($result->getMessage(), $result->getCode());
		}
	}
}