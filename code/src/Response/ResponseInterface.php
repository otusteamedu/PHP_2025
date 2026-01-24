<?php

namespace App\Response;

interface ResponseInterface{

	public function send(string $message, int $code);
}