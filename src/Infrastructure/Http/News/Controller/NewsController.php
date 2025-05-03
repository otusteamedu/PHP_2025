<?php

namespace App\Infrastructure\Http\News\Controller;

use App\Application\DTO\News\CreateNewsDTO;
use App\Application\DTO\Report\RequestReportDTO;
use App\Application\UseCase\CreateNewsUseCase;
use App\Application\UseCase\GenerateReportUseCase;
use App\Application\UseCase\GetNewsList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

//TODO контроллер на каждое дейстие

final class NewsController extends AbstractController
{
    public function __construct(
        protected CreateNewsUseCase     $createNewsUseCase,
        protected GenerateReportUseCase $generateReportUseCase,
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

    final public function create(Request $request): JsonResponse
    {
        try {
            $url = $request->request->get('url');
            $createdNewsDTO = new createNewsDTO($url);
            $createdNews = $this->createNewsUseCase->execute($createdNewsDTO);

            return $this->json(['STATUS' => 'OK', 'CREATED_NEWS' => $createdNews]);
        } catch(\Exception $exception) {
            return $this->json([
                'STATUS' => 'ERR',
                'MESSAGE' => $exception->getMessage(),
            ]);
        }
    }

    final public function generateReport(Request $request): JsonResponse
    {
        try {
            $arRequest = $request->toArray();
            if (!is_array($arRequest['news']) or empty($arRequest['news'])) {
                throw new \RuntimeException('There no news with such IDs');
            }

            $requestReportDTO = new RequestReportDTO($arRequest['news']);
            $arResult = $this->generateReportUseCase->execute($requestReportDTO);

            return $this->json(['STATUS' => 'OK', 'RESULT' => $arResult]);
        } catch(\Exception $exception) {
            return $this->json([
                'STATUS' => 'ERR',
                'MESSAGE' => $exception->getMessage(),
            ]);
        }
    }
}