<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Actions;

use App\Application\DTO\CreateReportRequestDTO;
use App\Application\UseCases\CreateReportRequestUseCase;
use App\Domain\Interfaces\ActionInterface;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class CreateReportAction implements ActionInterface
{
    public function __construct(
        private readonly CreateReportRequestUseCase $useCase
    ) {}

    public function supports(Request $request): bool
    {
        return $request->isPost() && $request->getPath() === '/api/report';
    }

    public function handle(Request $request): Response
    {
        $dto = CreateReportRequestDTO::fromArray($request->getBody());
        $result = $this->useCase->execute($dto);

        if ($result->success) {
            return Response::success($result->toArray());
        }

        return Response::error($result->message, 422, $result->errors);
    }
}
