<?php

namespace App\Response;

class Response{

	public function send($message, $code){
		http_response_code($code);
    	return $message;
	}
}