<?php

namespace App\Infrastructure\Http\News\Controller;

use App\Infrastructure\UseCase\GetNewsList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

final class NewsIndexController extends AbstractController
{
    public function __construct(
        protected GetNewsList           $getNewsList,
    ){}

    final public function index(): JsonResponse
    {
        try {
            $arDtoNews = $this->getNewsList->execute();
            return $this->json(['STATUS' => 'OK', 'NEWS' => $arDtoNews]);
        } catch(\Exception $exception) {
            return $this->json([
                'STATUS' => 'ERR',
                'MESSAGE' => $exception->getMessage(),
            ]);
        }
    }
}