<?php

namespace App\Application\Port;

use App\Domain\Entity\News;

//TODO перенести в Домен, реализация на слое инфраструктуры

interface NewsRepositoryInterface
{
    public function save(News $news): void;
    public function getList(): array;
}