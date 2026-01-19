<?php

namespace Fatnev\Price;

use Fatnev\Price\Config\Config;
use Fatnev\Price\Services\ProductService;
use Fatnev\Price\Services\PdfGenerator;

class PriceListGenerator
{
    public static function generate(): array
    {
        $config = new Config();
        $productService = new ProductService($config);
        $pdfGenerator = new PdfGenerator($config);
        
        try {
            $products = $productService->getAvailableProducts();
            $pdfResult = $pdfGenerator->createFromProducts($products);
            
            return [
                'success' => true,
                'file_path' => $pdfResult['file_path'],
                'file_url' => $pdfResult['file_url'],
                'file_name' => $pdfResult['file_name'],
                'count' => count($products)
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}