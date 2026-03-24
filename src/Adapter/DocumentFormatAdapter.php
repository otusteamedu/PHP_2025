<?php

declare(strict_types=1);

namespace App\Adapter;

use App\FactoryMethod\IDocument;

/**
 * DocumentFormatAdapter - паттерн Адаптер для преобразования форматов документов
 * 
 * Адаптирует старое API чтения документов (LegacyDocumentAPI) 
 * к новому интерфейсу с возможностью преобразования формата
 * 
 * Например:
 * - Читает документ в одном формате (через LegacyDocumentAPI)
 * - Преобразует содержимое (изменяет структуру, метаданные, etc)
 * - Сохраняет в новом формате (через IDocument)
 */
final class DocumentFormatAdapter implements IDocument
{
    private LegacyDocumentAPI $legacyAPI;
    private ?string $currentPath = null;
    private array $documentData = [];
    
    /** @var string Целевой формат для преобразования */
    private string $targetFormat;

    /**
     * @param LegacyDocumentAPI $legacyAPI Старое API для чтения
     * @param string $targetFormat Целевой формат (excel, pdf, csv, html)
     */
    public function __construct(
        ?LegacyDocumentAPI $legacyAPI = null,
        string $targetFormat = 'excel'
    ) {
        $this->legacyAPI = $legacyAPI ?? new LegacyDocumentAPI();
        $this->targetFormat = $targetFormat;
    }

    /**
     * Открывает документ и подготавливает к преобразованию
     */
    public function open(string $path): void
    {
        echo "[DocumentFormatAdapter] Opening file: {$path}\n";
        echo "[DocumentFormatAdapter] Target format: {$this->targetFormat}\n";
        
        $this->currentPath = $path;
        
        // Читаем документ через старое API
        $this->documentData = $this->legacyAPI->readDocumentContent($path);
        
        if (empty($this->documentData)) {
            echo "[DocumentFormatAdapter] Warning: File is empty\n";
        }
    }

    /**
     * Преобразует содержимое документа в целевой формат и сохраняет
     */
    public function save(string $content): void
    {
        if ($this->currentPath === null) {
            throw new \RuntimeException('File path not set. Call open() first.');
        }

        // Преобразуем содержимое в зависимости от целевого формата
        $transformedContent = $this->transformContent($content);
        
        // Определяем расширение файла на основе целевого формата
        $outputPath = $this->getOutputPath($this->currentPath);
        
        echo "[DocumentFormatAdapter] Transforming to {$this->targetFormat} format...\n";
        echo "[DocumentFormatAdapter] Original size: " . strlen($content) . " bytes\n";
        echo "[DocumentFormatAdapter] Transformed size: " . strlen($transformedContent) . " bytes\n";
        
        // Сохраняем преобразованное содержимое
        file_put_contents($outputPath, $transformedContent);
        
        echo "[DocumentFormatAdapter] Saved as: {$outputPath}\n";
    }

    /**
     * Загружает документ и преобразует его в целевой формат
     */
    public function load(string $path): string
    {
        echo "[DocumentFormatAdapter] Loading and transforming: {$path}\n";
        
        // Читаем через старое API
        $data = $this->legacyAPI->readDocumentContent($path);
        
        // Преобразуем содержимое в целевой формат
        $content = $data['content'] ?? '';
        
        return $this->transformContent($content);
    }

    /**
     * Закрывает документ
     */
    public function close(): void
    {
        $this->currentPath = null;
        $this->documentData = [];
        echo "[DocumentFormatAdapter] Document closed\n";
    }

    /**
     * Преобразует содержимое документа в целевой формат
     * 
     * Это главное место адаптации - преобразование содержимого
     */
    private function transformContent(string $content): string
    {
        return match($this->targetFormat) {
            'excel' => $this->transformToExcel($content),
            'pdf' => $this->transformToPDF($content),
            'csv' => $this->transformToCSV($content),
            'html' => $this->transformToHTML($content),
            default => $this->transformToExcel($content),
        };
    }

    /**
     * Преобразует в Excel-формат (CSV-подобный)
     */
    private function transformToExcel(string $content): string
    {
        $lines = explode("\n", $content);
        
        $csvContent = [];
        $csvContent[] = '"TRANSFORMED_TO_EXCEL_FORMAT"';
        $csvContent[] = '"Timestamp","' . date('Y-m-d H:i:s') . '"';
        $csvContent[] = '"Original_Lines","' . count($lines) . '"';
        $csvContent[] = '"Word_Count","' . str_word_count($content) . '"';
        $csvContent[] = '---';
        
        // Каждая строка оригинального документа - новая строка в Excel
        foreach ($lines as $index => $line) {
            if (!empty(trim($line))) {
                $csvContent[] = '"Line_' . ($index + 1) . '","' . str_replace('"', '""', $line) . '"';
            }
        }

        return implode("\n", $csvContent);
    }

    /**
     * Преобразует в PDF-подобный формат
     */
    private function transformToPDF(string $content): string
    {
        $output = [];
        $output[] = "%%PDF-1.4 (SIMULATED)";
        $output[] = "%CONVERTED_TO_PDF_FORMAT";
        $output[] = "1 0 obj";
        $output[] = "<< /Type /Catalog /Pages 2 0 R >>";
        $output[] = "endobj";
        $output[] = "2 0 obj";
        $output[] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
        $output[] = "endobj";
        $output[] = "3 0 obj";
        $output[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R >>";
        $output[] = "endobj";
        $output[] = "4 0 obj";
        $output[] = "<< /Length " . strlen($content) . " >>";
        $output[] = "stream";
        $output[] = "BT";
        $output[] = "/F1 12 Tf";
        $output[] = "50 750 Td";
        $output[] = "(" . str_replace("\n", "\\n", addslashes($content)) . ") Tj";
        $output[] = "ET";
        $output[] = "endstream";
        $output[] = "endobj";
        $output[] = "xref";
        $output[] = "0 5";
        $output[] = "0000000000 65535 f";
        $output[] = "trailer";
        $output[] = "<< /Size 5 /Root 1 0 R >>";
        $output[] = "%%EOF";

        return implode("\n", $output);
    }

    /**
     * Преобразует в CSV-формат
     */
    private function transformToCSV(string $content): string
    {
        $lines = explode("\n", $content);
        
        $csv = [];
        $csv[] = '"Type","Value"';
        $csv[] = '"Format Conversion","Word to CSV"';
        $csv[] = '"Date Converted","' . date('Y-m-d H:i:s') . '"';
        $csv[] = '"Total Lines","' . count($lines) . '"';
        $csv[] = '---';
        
        foreach ($lines as $line) {
            if (!empty(trim($line))) {
                $csv[] = '"Text","' . str_replace('"', '""', trim($line)) . '"';
            }
        }

        return implode("\n", $csv);
    }

    /**
     * Преобразует в HTML-формат
     */
    private function transformToHTML(string $content): string
    {
        $lines = explode("\n", $content);
        
        $html = [];
        $html[] = '<!DOCTYPE html>';
        $html[] = '<html lang="ru">';
        $html[] = '<head>';
        $html[] = '  <meta charset="UTF-8">';
        $html[] = '  <title>Converted Document</title>';
        $html[] = '  <style>';
        $html[] = '    body { font-family: Arial, sans-serif; margin: 20px; }';
        $html[] = '    .metadata { background: #f0f0f0; padding: 10px; margin-bottom: 20px; }';
        $html[] = '    .content { line-height: 1.6; }';
        $html[] = '  </style>';
        $html[] = '</head>';
        $html[] = '<body>';
        $html[] = '  <div class="metadata">';
        $html[] = '    <p><strong>Document converted to HTML</strong></p>';
        $html[] = '    <p>Conversion Date: ' . date('Y-m-d H:i:s') . '</p>';
        $html[] = '    <p>Total Lines: ' . count($lines) . '</p>';
        $html[] = '  </div>';
        $html[] = '  <div class="content">';
        
        foreach ($lines as $line) {
            if (!empty(trim($line))) {
                $html[] = '    <p>' . htmlspecialchars($line) . '</p>';
            }
        }
        
        $html[] = '  </div>';
        $html[] = '</body>';
        $html[] = '</html>';

        return implode("\n", $html);
    }

    /**
     * Получает выходной путь файла с новым расширением
     */
    private function getOutputPath(string $inputPath): string
    {
        $ext = match($this->targetFormat) {
            'excel' => 'csv',
            'pdf' => 'pdf',
            'csv' => 'csv',
            'html' => 'html',
            default => 'txt',
        };

        $dir = dirname($inputPath);
        $filename = basename($inputPath, '.' . pathinfo($inputPath, PATHINFO_EXTENSION));
        
        return "{$dir}/{$filename}_converted.{$ext}";
    }
}
