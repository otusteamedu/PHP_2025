<?php

namespace App\Response;

class Response{

	public function sendReply($message, $code){
		http_response_code($code);
    	return $message;
	}
}