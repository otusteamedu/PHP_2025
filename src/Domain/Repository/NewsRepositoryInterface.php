<?php

namespace App\Domain\Repository;

use App\Domain\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface NewsRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function save(News $news): void;
    public function getList(): array;
    public function getByIds(array $arNewsIds):array;
}