<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Controller;

use App\News\Application\UseCase\GetPaginatedNews\GetPaginatedNewsRequest;
use App\News\Application\UseCase\GetPaginatedNews\GetPaginatedNewsUseCase;
use App\News\Domain\Repository\NewsFilter;
use App\Shared\Domain\Repository\Pager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\Assert\Assert;

#[Route('/api/news/list', name: 'app_api_news_list', methods: ['POST'])]
class NewsListAction extends AbstractController
{
    public function __construct(
        private readonly GetPaginatedNewsUseCase $getPaginatedNewsUseCase,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $page = $data['page'] ?? null;
        if (!$page) {
            $page = Pager::DEFAULT_PAGE;
        }
        Assert::integer($page, message: 'Page must be a positive integer.');

        $limit = $data['limit'] ?? null;
        if (!$limit) {
            $limit = Pager::DEFAULT_LIMIT;
        }
        Assert::integer($limit, message: 'Limit must be a positive integer.');

        $request = new GetPaginatedNewsRequest(new NewsFilter(
            new Pager($page, $limit),
            $data['search'] ?? null
        ));
        $result = ($this->getPaginatedNewsUseCase)($request);

        return new JsonResponse($result);
    }
}
