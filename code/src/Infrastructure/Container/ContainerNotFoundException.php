<?php

declare(strict_types=1);

namespace App\Infrastructure\Container;

use App\Domain\Interfaces\NotFoundExceptionInterface;
use Exception;

class ContainerNotFoundException extends Exception implements NotFoundExceptionInterface {}
