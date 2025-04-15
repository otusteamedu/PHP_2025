<?php

namespace App\Exceptions;

class ApiException extends \Exception
{
  protected $code = 400;
}