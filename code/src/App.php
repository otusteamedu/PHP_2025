<?php

namespace App;

use App\Response\Response;
use App\Response\ResponseInterface;
use App\Service\Validator;
use App\Service\ValidatorInterface;

class App
{

	private ValidatorInterface $validator;
	private ResponseInterface $response;

    public function __construct(ValidatorInterface $validator, ResponseInterface $response)
    {
		$this->validator = $validator;
    	$this->response = $response;
    }

	public function run()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$string = $_POST['string'] ? $_POST['string'] : '';
			$result = $this->validator->validate($string);
   			return $this->response->send($result->getMessage(), $result->getCode());
		}
	}
}