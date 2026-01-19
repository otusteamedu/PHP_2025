<?php

namespace Fatnev\Price;

class Config
{
    private array $productIblocks = [26, 27];
    private int $defaultLimit = 500;
    
    public function getProductIblocks(): array
    {
        return $this->productIblocks;
    }
    
    public function getDefaultLimit(): int
    {
        return $this->defaultLimit;
    }
    
    public function getPriceListPath(): string
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . "/upload/managers/price/";
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        return $path;
    }
    
    public function getLogoUrl(): string
    {
        return 'https://' . $_SERVER['HTTP_HOST'] . '/upload/managers/logo/logo.png';
    }
    
    public function getLogoPath(): string
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/upload/managers/logo/logo.png';
    }
    
    public function getLogoDataUri(): string
    {
        $logoPath = $this->getLogoPath();
        
        if (file_exists($logoPath)) {
            $imageData = file_get_contents($logoPath);
            $mimeType = mime_content_type($logoPath);
            $base64 = base64_encode($imageData);
            
            return 'data:' . $mimeType . ';base64,' . $base64;
        }
        
        // Если файла нет локально, возвращаем URL
        return $this->getLogoUrl();
    }
    
    public function getBaseUrl(): string
    {
        return 'https://' . $_SERVER['HTTP_HOST'];
    }
}