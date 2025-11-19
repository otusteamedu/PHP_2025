<?php

namespace App\Base\Exceptions;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class ServiceContainerNotFoundException extends RuntimeException implements ContainerExceptionInterface
{

}