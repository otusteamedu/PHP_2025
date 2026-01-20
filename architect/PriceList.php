<?php

namespace Fatnev\Price;

use Fatnev\Price\UseCases\GeneratePriceList;
use Fatnev\Price\Domain\ProductService;
use Fatnev\Price\Factory\PdfGenerator;
use Fatnev\Price\Config\Config;

class PriceList
{
    public static function generate(): array
    {
        $config = new Config();
        $productService = new ProductService($config);
        $pdfGenerator = new PdfGenerator($config);
        $useCase = new GeneratePriceList($productService, $pdfGenerator, $config);
        
        return $useCase->execute();
    }
}