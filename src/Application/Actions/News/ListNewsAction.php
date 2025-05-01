<?php

namespace App\Application\Actions\News;

use App\Application\Actions\News\NewsAction;
use Psr\Http\Message\ResponseInterface as Response;

class ListNewsAction extends NewsAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $news = $this->newsRepository->findAll();
        $this->logger->info("News list was viewed.");

        return $this->respondWithData($news);
    }
}