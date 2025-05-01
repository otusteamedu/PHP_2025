<?php

namespace App\Application\Actions\News;

use Psr\Http\Message\ResponseInterface as Response;

class ViewNewsAction extends NewsAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $news = $this->newsRepository->findById((int)$this->resolveArg('id'));
        $this->logger->info("News with id=" . (string)$this->resolveArg('id')." viewed.");
        return $this->respondWithData($news);
    }
}