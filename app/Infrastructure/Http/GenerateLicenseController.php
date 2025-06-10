<?php
declare(strict_types=1);

namespace Infrastructure\Http;

use Application\UseCase\GenerateLicense\GenerateLicenseUseCase;
use Application\UseCase\GenerateLicense\GenerateLicenseRequest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;

class GenerateLicenseController extends AbstractFOSRestController
{
    public function __construct(
        private GenerateLicenseUseCase $useCase,
    )
    {
    }

    public function __invoke(GenerateLicenseRequest $request): Response
    {
        try {
            $response = ($this->useCase)($request);
            $view = $this->view($response, 201);
        } catch (\Throwable $e) {
            // todo В реальности используются более сложные обработчики ошибок
            $errorResponse = [
                'message' => $e->getMessage()
            ];
            $view = $this->view($errorResponse, 400);
        }

        return $this->handleView($view);
    }
}