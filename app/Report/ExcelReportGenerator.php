<?php

declare(strict_types=1);

namespace App\Report;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class ExcelReportGenerator
{
    public function generate(array $analysis, string $dateFrom, string $dateTo): void
    {
        $spreadsheet = new Spreadsheet();
        $this->addSheet($spreadsheet, 'Опоздавшие', $analysis['late'], $dateFrom, $dateTo);
        $this->addSheet($spreadsheet, 'Пришли рано', $analysis['came_early'], $dateFrom, $dateTo);
        $this->addSheet($spreadsheet, 'Ушли рано', $analysis['left_early'], $dateFrom, $dateTo);
        $this->addSheet($spreadsheet, 'Не вышли', $analysis['did_not_leave'], $dateFrom, $dateTo);
        $this->addSheet($spreadsheet, 'Опоздали после обеда', $analysis['late_after_lunch'], $dateFrom, $dateTo);
        $this->addSheet($spreadsheet, 'Ушли рано на обед', $analysis['left_early_to_lunch'], $dateFrom, $dateTo);
        $this->addNeverCameSheet($spreadsheet, $analysis['never_came'], $dateFrom, $dateTo);

        $spreadsheet->setActiveSheetIndex(0);

        $filename = "skyd_report_{$dateFrom}_" . date('Ymd_His') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function addSheet(Spreadsheet $spreadsheet, string $title, array $data, string $dateFrom, string $dateTo): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle($title);

        $this->addHeader($sheet, $title, $dateFrom, $dateTo);
        $this->fillData($sheet, $data);
    }

    private function addNeverCameSheet(Spreadsheet $spreadsheet, array $data, string $dateFrom, string $dateTo): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Не приходили');
        $this->addHeader($sheet, 'Не приходили', $dateFrom, $dateTo);
        $this->fillNeverCameData($sheet, $data);
    }

    private function addHeader(Worksheet $sheet, string $title, string $dateFrom, string $dateTo): void
    {
        $sheet->setCellValue('A1', 'Система СКУД');
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A2', "Отчёт с {$dateFrom} по {$dateTo}");
        $sheet->mergeCells('A2:E2');
        $sheet->setCellValue('A3', $title);
        $sheet->mergeCells('A3:E3');

        $headerStyle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:E3')->applyFromArray($headerStyle);
    }

    private function fillData(Worksheet $sheet, array $data): void
    {
        // Заголовки таблицы
        $headers = ['№', 'ФИО', 'Время входа', 'Время выхода', 'Статус'];
        foreach ($headers as $i => $header) {
            $sheet->setCellValueByColumnAndRow($i + 1, 4, $header);
        }

        $row = 5;
        foreach ($data as $i => $item) {
            $sheet->setCellValueByColumnAndRow(1, $row, $i + 1);
            $sheet->setCellValueByColumnAndRow(2, $row, $item['fio'] ?? '');
            $sheet->setCellValueByColumnAndRow(3, $row, $item['dateIn'] ?? '');
            $sheet->setCellValueByColumnAndRow(4, $row, $item['dateOut'] ?? '');
            $sheet->setCellValueByColumnAndRow(5, $row, $item['status_html'] ?? '');
            $row++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function fillNeverCameData(Worksheet $sheet, array $data): void
    {
        $headers = ['№', 'ФИО', 'Код'];
        foreach ($headers as $i => $header) {
            $sheet->setCellValueByColumnAndRow($i + 1, 4, $header);
        }

        $row = 5;
        foreach ($data as $i => $user) {
            $sheet->setCellValueByColumnAndRow(1, $row, $i + 1);
            $sheet->setCellValueByColumnAndRow(2, $row, $user['fio'] ?? '');
            $sheet->setCellValueByColumnAndRow(3, $row, $user['code'] ?? '');
            $row++;
        }
    }
}