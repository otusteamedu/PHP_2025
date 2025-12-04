<?php

namespace Response;

class Response{

	public function sendReply($message, $code){
		http_response_code($code);
		if ($code == 400){
			throw new \Exception($message);
		}
    	return $message;
	}
}