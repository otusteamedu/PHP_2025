<?php

declare(strict_types=1);

namespace App\News\Application\DTO;

use App\News\Domain\Entity\News;

class NewsDTOTransformer implements NewsDTOTransformerInterface
{
    public function fromEntity(News $news): NewsDTO
    {
        $newsDTO = new NewsDTO();
        $newsDTO->id = $news->getId()->toString();
        $newsDTO->title = $news->getTitle()->getValue();
        $newsDTO->link = $news->getLink()->getValue();
        $newsDTO->created_at = $news->getCreatedAt();

        return $newsDTO;
    }

    public function fromEntityList(array $news): array
    {
        $newsDTOs = [];
        foreach ($news as $new) {
            $newsDTOs[] = $this->fromEntity($new);
        }

        return $newsDTOs;
    }
}
