<?php

declare(strict_types=1);

namespace App\Controller;

use App\Report\ExcelReportGenerator;
use App\Service\SKYDService;
use DateTimeImmutable;

final class SKYDController
{
    public function __construct(
        private readonly SKYDService $service,
        private readonly ExcelReportGenerator $excelGenerator
    ) {}

    public function handleRequest(): void
    {

        // Получение отчёта
        if (isset($_GET['report']) || isset($_POST['report'])) {
            $input = isset($_POST['report']) ? $_POST : $_GET;

            $startRaw = $input['dateStart'] ?? date('Y-m-d 00:00:00', strtotime('-30 days'));
            $endRaw = $input['dateEnd'] ?? date('Y-m-d 23:59:59');

            $startDateTime = $this->parseStrictDateTime((string)$startRaw);
            if ($startDateTime === null) {
                $this->respondJsonError('dateStart must be in format Y-m-d H:i:s.', 400);
                return;
            }

            $endDateTime = $this->parseStrictDateTime((string)$endRaw);
            if ($endDateTime === null) {
                $this->respondJsonError('dateEnd must be in format Y-m-d H:i:s.', 400);
                return;
            }

            if ($startDateTime > $endDateTime) {
                $this->respondJsonError('dateStart must be less than or equal to dateEnd.', 400);
                return;
            }

            $start = $startDateTime->format('Y-m-d H:i:s');
            $end = $endDateTime->format('Y-m-d H:i:s');

            $analysis = $this->service->getReport($start, $end);

            // Excel
            if (isset($_GET['excel']) || isset($_POST['export_excel'])) {
                $this->excelGenerator->generate($analysis, $start, $end);
                return;
            }

            // JSON ответ (для фронта)
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => true,
                'data' => $analysis
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Если ничего не подошло — показываем форму
        include __DIR__ . '/../../SKYD_template2.php'; 
    }

    private function parseStrictDateTime(string $value): ?DateTimeImmutable
    {
        $dateTime = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value);
        $errors = DateTimeImmutable::getLastErrors();

        if ($dateTime === false) {
            return null;
        }

        if (($errors['warning_count'] ?? 0) > 0 || ($errors['error_count'] ?? 0) > 0) {
            return null;
        }

        if ($dateTime->format('Y-m-d H:i:s') !== $value) {
            return null;
        }

        return $dateTime;
    }

    private function respondJsonError(string $message, int $statusCode): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => false,
            'error' => $message,
        ], JSON_UNESCAPED_UNICODE);
    }
}