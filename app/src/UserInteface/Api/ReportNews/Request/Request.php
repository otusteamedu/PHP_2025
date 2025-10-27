<?php

declare(strict_types=1);

namespace App\UserInteface\Api\ReportNews\Request;

use Symfony\Component\Validator\Constraints as Assert;

class Request
{
    #[Assert\NotBlank]
    #[Assert\Type('array')]
    public mixed $ids = null;
}
