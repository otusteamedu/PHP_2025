<?php

namespace Fatnev\Price\UseCases;

use Fatnev\Price\Domain\ProductService;
use Fatnev\Price\Factory\PdfGenerator;
use Fatnev\Price\Config\Config;

class GeneratePriceList
{
    private ProductService $productService;
    private PdfGenerator $pdfGenerator;
    private Config $config;
    
    public function __construct(
        ProductService $productService,
        PdfGenerator $pdfGenerator,
        Config $config
    ) {
        $this->productService = $productService;
        $this->pdfGenerator = $pdfGenerator;
        $this->config = $config;
    }
    
    public function execute(): array
    {
        try {
            $products = $this->productService->getAvailableProducts();
            $pdfResult = $this->pdfGenerator->createFromProducts($products);
            
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