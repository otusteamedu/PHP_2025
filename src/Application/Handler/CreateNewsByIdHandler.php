<?php

namespace App\Application\Handler;

use App\Application\Query\GetNewsByIdQuery;
use App\Application\Port\NewsRepository;

class CreateNewsByIdHandler
{
    protected NewsRepository $newsRepository;
    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    public function handle(GetNewsByIdQuery $query)
    {
        return $this->newsRepository->findById($query->id);
    }
}