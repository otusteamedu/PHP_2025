<?php

namespace App\Application\Assembler;

use App\Application\DTO\NewsDTO;
use App\Domain\Entity\News;
use App\Domain\ValueObject\News\Title;
use App\Domain\ValueObject\News\Url;
use App\Domain\ValueObject\News\CreateDate;

class NewsAssembler
{
    public function toDTO(News $news): NewsDTO
    {
        $dto = new NewsDTO();
        $dto->id = $news->getId();
        $dto->title = $news->getTitle();
        $dto->url = $news->getUrl();
        $dto->createDate = $news->getCreateDate();
        return $dto;
    }

    public function toEntity(NewsDTO $dto): News
    {
        return new News(
            new Title($dto->title),
            new Url($dto->url),
            new CreateDate($dto->createDate)
        );
    }
}