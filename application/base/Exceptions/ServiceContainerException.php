<?php

namespace App\Base\Exceptions;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class ServiceContainerException extends RuntimeException implements ContainerExceptionInterface
{


}