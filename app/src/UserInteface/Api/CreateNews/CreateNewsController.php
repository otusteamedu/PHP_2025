<?php

declare(strict_types=1);

namespace App\UserInteface\Api\CreateNews;

use App\Application\CreateNews\CreateNewsHandler;
use App\Application\CreateNews\CreateNewsQuery;
use App\UserInteface\Api\CreateNews\Request\Request as CreateNewsRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class CreateNewsController
{
    #[Route('/v1/create/news', name: 'create_news', methods: Request::METHOD_POST)]
    public function create(
        #[MapRequestPayload] CreateNewsRequest $request,
        CreateNewsHandler $handler
    ): JsonResponse
    {
        $query = new CreateNewsQuery(url: $request->url);
        $output = $handler($query);

        return new JsonResponse(['id' => $output->id]);
    }
}
