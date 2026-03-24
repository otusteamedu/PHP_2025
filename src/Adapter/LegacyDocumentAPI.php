<?php

declare(strict_types=1);

namespace App\Adapter;

/**
 * LegacyDocumentAPI - старая библиотека для работы с документами
 * Может читать документы, но имеет свой собственный API
 */
final class LegacyDocumentAPI
{
    /**
     * Читает содержимое документа в старом формате
     * (например, как его видит старая система)
     */
    public function readDocumentContent(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [];
        }

        $content = file_get_contents($filePath);
        
        // Старый API возвращает структурированные данные
        echo "[LegacyDocumentAPI] Reading: {$filePath}\n";
        
        return [
            'filename' => basename($filePath),
            'size' => filesize($filePath),
            'content' => $content,
            'timestamp' => filemtime($filePath),
            'lines' => explode("\n", $content),
            'word_count' => str_word_count($content),
        ];
    }

    /**
     * Пишет документ в старом формате
     */
    public function writeDocumentData(string $filePath, array $documentData): void
    {
        $output = "=== LEGACY FORMAT ===\n";
        $output .= "File: {$documentData['filename']}\n";
        $output .= "Size: {$documentData['size']} bytes\n";
        $output .= "Words: {$documentData['word_count']}\n";
        $output .= "=== CONTENT ===\n";
        $output .= $documentData['content'];

        file_put_contents($filePath, $output);
        echo "[LegacyDocumentAPI] Written: {$filePath}\n";
    }

    /**
     * Экспортирует содержимое в форматированный вид
     */
    public function exportAsFormatted(array $documentData): string
    {
        $output = [];
        $output[] = "Filename: " . $documentData['filename'];
        $output[] = "Total Words: " . $documentData['word_count'];
        $output[] = "Total Lines: " . count($documentData['lines']);
        $output[] = "File Size: " . $documentData['size'] . " bytes";
        $output[] = "---";
        $output[] = $documentData['content'];

        return implode("\n", $output);
    }
}

