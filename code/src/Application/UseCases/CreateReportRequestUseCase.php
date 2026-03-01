<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\DTO\CreateReportRequestDTO;
use App\Application\DTO\ReportResponseDTO;
use App\Domain\Entities\ReportRequest;
use App\Domain\Interfaces\ReportRequestServiceInterface;

class CreateReportRequestUseCase
{
    public function __construct(
        private readonly ReportRequestServiceInterface $reportRequestService
    ) {}

    public function execute(CreateReportRequestDTO $dto): ReportResponseDTO
    {
        $errors = $dto->validate();

        if (!empty($errors)) {
            return new ReportResponseDTO(
                success: false,
                message: 'Ошибка валидации данных',
                errors: $errors
            );
        }

        $reportRequest = new ReportRequest(
            name: $dto->name,
            email: $dto->email,
            year: $dto->year,
            createdAt: new \DateTimeImmutable()
        );

        $this->reportRequestService->queueReportRequest($reportRequest);

        return new ReportResponseDTO(
            success: true,
            message: 'Задание на формирование выписки добавлено в очередь. Результат будет отправлен на указанный email.'
        );
    }
}
