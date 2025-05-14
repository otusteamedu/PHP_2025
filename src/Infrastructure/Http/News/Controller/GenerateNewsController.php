<?php

namespace App\Infrastructure\Http\News\Controller;

use App\Application\DTO\Report\RequestReportDTO;
use App\Application\UseCase\GenerateReportUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class GenerateNewsController extends AbstractController
{
    public function __construct(
        protected GenerateReportUseCase $generateReportUseCase,
    ){}

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