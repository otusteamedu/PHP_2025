<?php

namespace App\Application\Actions\News;

use App\Application\Actions\News\NewsAction;
use Psr\Http\Message\ResponseInterface as Response;

class ReportNewsAction extends NewsAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $news = $this->newsRepository->findByIds();
        $this->logger->info("News list was viewed.");

        return $this->respondWithData($news);
    }
}