<?php

namespace App\Base\Exceptions\ServiceContainer;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class ServiceContainerNotFoundException extends RuntimeException implements ContainerExceptionInterface
{

}