<?php

declare(strict_types=1);

namespace App\UserInteface\Api\ListNews\Request;

use Symfony\Component\Validator\Constraints as Assert;

class Request
{
    public const int DEFAULT_PAGE = 1;
    public const int DEFAULT_LIMIT = 60;
    public const int MAX_PAGE = 100;

    #[Assert\Sequentially([
        new Assert\Type('integer'),
    ])]
    public int $limit = self::DEFAULT_LIMIT;

    #[Assert\Sequentially([
        new Assert\Type('integer'),
        new Assert\Range(min: 1, max: self::MAX_PAGE),
    ])]
    public int $page = self::DEFAULT_PAGE;

}