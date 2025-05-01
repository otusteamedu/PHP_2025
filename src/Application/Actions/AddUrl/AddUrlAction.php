<?php

namespace App\Application\Actions\AddUrl;

use App\Application\Actions\Action;
use App\Application\UseCase\AddUrl\AddUrlRequest;
use App\Application\UseCase\AddUrl\AddUrlUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class AddUrlAction extends Action
{
    protected ?AddUrlUseCase $addUrlUseCase;
    public function __construct(LoggerInterface $logger, AddUrlUseCase $addUrlUseCase)
    {
        parent::__construct($logger);
//        var_dump($addUrlUseCase);
//        exit();
        $this->addUrlUseCase = $addUrlUseCase;
    }

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $newsID = ($this->addUrlUseCase)(new AddUrlRequest($this->getFormData()['url']));

        $this->logger->info("New URL added with id '{$newsID->id}'.");

        return $this->respondWithData($newsID->id);
    }
}