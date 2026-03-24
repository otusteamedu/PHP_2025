<?php

declare(strict_types=1);

namespace App\Adapter;

use App\FactoryMethod\IDocument;

/**
 * DocumentAdapter - паттерн Адаптер (Adapter Pattern)
 * 
 * Адаптирует LegacyDocumentAPI к интерфейсу IDocument
 * Позволяет использовать старый код в новой архитектуре
 */
final class DocumentAdapter implements IDocument
{
    private LegacyDocumentAPI $legacyAPI;

    public function __construct(?LegacyDocumentAPI $legacyAPI = null)
    {
        $this->legacyAPI = $legacyAPI ?? new LegacyDocumentAPI();
    }

    /**
     * Открывает документ используя старый API
     */
    public function open(string $path): void
    {
        echo "[DocumentAdapter] Opening: {$path}\n";
    }

    /**
     * Сохраняет содержимое используя старый API
     */
    public function save(string $content): void
    {
        $data = [
            'filename' => 'document.txt',
            'size' => strlen($content),
            'content' => $content,
            'word_count' => str_word_count($content),
        ];
        
        echo "[DocumentAdapter] Saving document\n";
    }

    /**
     * Загружает содержимое используя старый API
     */
    public function load(string $path): string
    {
        $data = $this->legacyAPI->readDocumentContent($path);
        return $data['content'] ?? '';
    }

    /**
     * Закрывает документ
     */
    public function close(): void
    {
        echo "[DocumentAdapter] Closed\n";
    }
}
