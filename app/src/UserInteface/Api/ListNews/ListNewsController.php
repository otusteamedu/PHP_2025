<?php

declare(strict_types=1);

namespace App\UserInteface\Api\ListNews;

use App\Application\ListNews\ListNewsHandler;
use App\Application\ListNews\ListNewsQuery;
use App\UserInteface\Api\ListNews\Request\Request as ListNewsRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class ListNewsController
{
    #[Route('/v1/list/news', name: 'list_news', methods: Request::METHOD_GET)]
    public function list(
        #[MapRequestPayload] ListNewsRequest $request,
        ListNewsHandler $handler,
    ): JsonResponse
    {
        $query = new ListNewsQuery(
            page: $request->page,
            limit: $request->limit,
        );

        $output = $handler($query);

        return new JsonResponse($output);
    }
}
