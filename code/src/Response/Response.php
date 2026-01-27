<?php

namespace App\Response;

class Response implements ResponseInterface
{
	public function send($message, $code): string
	{
		http_response_code($code);
    	return $message;
	}
}