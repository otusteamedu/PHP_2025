<?php

namespace Fatnev\Price;

use Bitrix\Main\Loader;

class ProductService
{
    private Config $config;
    
    public function __construct(Config $config)
    {
        Loader::includeModule('iblock');
        Loader::includeModule('catalog');
        $this->config = $config;
    }
    
    public function getAvailableProducts(?int $limit = null): array
    {
        $limit = $limit ?? $this->config->getDefaultLimit();
        $products = [];
        $page = 0;
        
        while (count($products) < $limit) {
            $batch = $this->getProductBatch($page);
            if (empty($batch)) break;
            
            foreach ($batch as $item) {
                if ($this->isProductAvailable($item)) {
                    $products[] = $item;
                    if (count($products) >= $limit) break 2;
                }
            }
            $page++;
        }
        
        return $products;
    }
    
    private function getProductBatch(int $page, int $batchSize = 100): array
    {
        $offset = $page * $batchSize;
        $products = [];
        
        $res = \CIBlockElement::GetList(
            ['NAME' => 'ASC'],
            [
                'IBLOCK_ID' => $this->config->getProductIblocks(),
                'ACTIVE' => 'Y',
            ],
            false,
            ['nTopCount' => $batchSize, 'nOffset' => $offset],
            ['ID', 'NAME', 'IBLOCK_ID']
        );
        
        while ($element = $res->Fetch()) {
            $products[] = [
                'id' => (int)$element['ID'],
                'name' => $element['NAME'],
                'iblock_id' => (int)$element['IBLOCK_ID'],
                'price' => $this->getProductPrice((int)$element['ID']),
                'quantity' => $this->getProductQuantity((int)$element['ID'])
            ];
        }
        
        return $products;
    }
    
    private function getProductPrice(int $productId): float
    {
        $price = \Bitrix\Catalog\PriceTable::getList([
            'filter' => ['PRODUCT_ID' => $productId],
            'select' => ['PRICE'],
            'limit' => 1
        ])->fetch();
        
        return $price ? (float)$price['PRICE'] : 0;
    }
    
    private function getProductQuantity(int $productId): float
    {
        $product = \Bitrix\Catalog\ProductTable::getList([
            'filter' => ['ID' => $productId],
            'select' => ['QUANTITY', 'AVAILABLE'],
        ])->fetch();
        
        return ($product && $product['AVAILABLE'] == 'Y') ? (float)$product['QUANTITY'] : 0;
    }
    
    private function isProductAvailable(array $product): bool
    {
        return $product['quantity'] > 0 && $product['price'] > 0;
    }
}