<?php

namespace App\Base\Exceptions;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class NotFoundException extends RuntimeException implements ContainerExceptionInterface
{

}