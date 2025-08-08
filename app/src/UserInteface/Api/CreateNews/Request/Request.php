<?php

declare(strict_types=1);

namespace App\UserInteface\Api\CreateNews\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class Request
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Url]
    public mixed $url = null;
}
