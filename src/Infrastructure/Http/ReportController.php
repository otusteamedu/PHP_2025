<?php declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\UseCase\ReportNews\ReportNewsRequest;
use App\Application\UseCase\ReportNews\ReportNewsUseCase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ReportController
{
    public function __construct(
        private ReportNewsUseCase $useCase,
    )
    {
    }

    #[Route('/api/v1/report', name: 'report', methods: ['POST'])]
    public function __invoke(Request $request): BinaryFileResponse|JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ids = $data['ids'] ?? [];

        try {
            $reportRequest = new ReportNewsRequest($ids);
            $reportResponse = ($this->useCase)($reportRequest);

            $response = new BinaryFileResponse($reportResponse->filepath);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                basename($reportResponse->getFileName())
            );

            return $response;
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
