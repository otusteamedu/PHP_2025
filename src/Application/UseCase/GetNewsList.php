<?php

namespace App\Application\UseCase;

use App\Domain\Repository\NewsRepositoryInterface;

class GetNewsList
{
    public function __construct(
        private NewsRepositoryInterface $newsRepository,
    ){}

    public function execute():array
    {
        return $this->newsRepository->getList();
    }

}