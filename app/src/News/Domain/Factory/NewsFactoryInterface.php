<?php
declare(strict_types=1);


namespace App\News\Domain\Factory;

use App\News\Domain\Entity\News;

interface NewsFactoryInterface
{
    public function create(string $title, string $link): News;
}