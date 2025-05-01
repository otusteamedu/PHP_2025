<?php

declare(strict_types=1);

namespace App\Application\Actions\News;

use App\Application\Actions\Action;
use App\Domain\Repository\NewsRepositoryInterface;
use Psr\Log\LoggerInterface;

abstract class NewsAction extends Action
{
    protected NewsRepositoryInterface $newsRepository;

    public function __construct(LoggerInterface $logger, NewsRepositoryInterface $newsRepository)
    {
        parent::__construct($logger);
        $this->newsRepository = $newsRepository;
    }
}
