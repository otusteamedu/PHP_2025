<?php

declare(strict_types=1);

namespace App\Controller;

use App\Report\ExcelReportGenerator;
use App\Service\SKYDService;

final class SKYDController
{
    public function __construct(
        private readonly SKYDService $service,
        private readonly ExcelReportGenerator $excelGenerator
    ) {}

    public function handleRequest(): void
    {
        // Синхронизация пользователей
        if (isset($_GET['sync_users'])) {
            $this->service->syncUsers();
            echo "Пользователи синхронизированы";
            return;
        }

        // Получение отчёта
        if (isset($_GET['report']) || isset($_POST['report'])) {
            $start = $_REQUEST['dateStart'] ?? date('Y-m-d 00:00:00', strtotime('-30 days'));
            $end   = $_REQUEST['dateEnd']   ?? date('Y-m-d 23:59:59');

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

        // Если ничего не подошло — показываем форму (можно заменить на шаблон)
        include __DIR__ . '/../../SKYD_template2.php'; // твой старый шаблон пока оставляем
    }
}