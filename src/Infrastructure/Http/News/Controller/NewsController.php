<?php

namespace App\Infrastructure\Http\News\Controller;

use App\Application\Command\CreateNewsCommand;
use App\Application\Command\GenerateNewsReport;
use App\Application\Command\GetNewsList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

//TODO контроллер на каждое дейстие

final class NewsController extends AbstractController
{
    public function __construct(
        protected CreateNewsCommand $createNewsCommand,
        protected GenerateNewsReport $generateNewsReport,
        protected GetNewsList $getNewsList,
    ){}

    final public function index(): JsonResponse
    {
        try {
            $arNews = $this->getNewsList->execute();
            return $this->json(['STATUS' => 'OK', 'NEWS' => $arNews]);
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
            $createdNews = $this->createNewsCommand->execute($url);
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
            $arResult = $this->generateNewsReport->execute($arRequest['news']);
            return $this->json(['STATUS' => 'OK', 'RESULT' => $arResult]);
        } catch(\Exception $exception) {
            return $this->json([
                'STATUS' => 'ERR',
                'MESSAGE' => $exception->getMessage(),
            ]);
        }
    }
}