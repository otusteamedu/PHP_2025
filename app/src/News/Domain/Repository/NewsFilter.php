<?php
declare(strict_types=1);


namespace App\News\Domain\Repository;

use App\Shared\Domain\Repository\Pager;

class NewsFilter
{
    public function __construct(
        public ?Pager  $pager = null,
        public ?string $search = null,
    )
    {
    }

}