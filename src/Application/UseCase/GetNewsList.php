<?php

namespace App\Application\UseCase;

use App\Application\DTO\News\ResponseNewsDTO;
use App\Domain\Repository\NewsRepositoryInterface;

class GetNewsList
{
    public function __construct(
        private NewsRepositoryInterface $newsRepository,
    ){}

    public function execute():array
    {
        $arDtoNews = [];
        $arNews = $this->newsRepository->getList();
        if (!empty($arNews)) {
            foreach ($arNews as $el) {
                $arDtoNews[] = new ResponseNewsDTO(
                    $el->getId(),
                    $el->getTitle(),
                    $el->getUrl(),
                    $el->getCreateDate(),
                );
            }
        }

        return $arDtoNews;
    }

}