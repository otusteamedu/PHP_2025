<?php

namespace Infrastructure\Factory;

use \Domain\Factory\NewsFactoryInterface;
use \Domain\Entity\News;
use \Domain\ValueObject\Url;

class CommonNewsFactory implements NewsFactoryInterface
{

    public function create(string $url): News
    {
        return new News(
            new Url($url)
        );
    }
}