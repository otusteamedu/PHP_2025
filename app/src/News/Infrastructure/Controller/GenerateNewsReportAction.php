<?php
declare(strict_types=1);


namespace App\News\Infrastructure\Controller;

use App\News\Application\UseCase\GenerateNewsReport\GenerateNewsReportRequest;
use App\News\Application\UseCase\GenerateNewsReport\GenerateNewsReportUseCase;
use Psr\Log\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/api/news/report/generate', name: 'app_api_news_report_generate', methods: ['POST'])]
class GenerateNewsReportAction extends AbstractController
{
    public function __construct(
        private readonly GenerateNewsReportUseCase $generateNewsReportUseCase,
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $newsIds = $data['news_ids'] ?? null;
        if ($newsIds === null) {
            throw new InvalidArgumentException('NewsIds is null');
        }
        $request = new GenerateNewsReportRequest(...$newsIds);
        $result = ($this->generateNewsReportUseCase)($request);

        $link = $this->generateUrl(DownloadNewsReportAction::class, ['fileName' => $result->pathToFile], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse(compact('link'));
    }

}