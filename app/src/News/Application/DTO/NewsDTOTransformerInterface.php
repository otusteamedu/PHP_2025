<?php

declare(strict_types=1);

namespace App\News\Application\DTO;

use App\News\Domain\Entity\News;

interface NewsDTOTransformerInterface
{
    public function fromEntity(News $news): NewsDTO;

    public function fromEntityList(array $news): array;
}
