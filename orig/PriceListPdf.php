<?php

namespace Fatnev\Price;

use Dompdf\Dompdf;
use Dompdf\Options;
use Bitrix\Main\Loader;

class PriceListPdf
{
    private $limit = 500; // Лимит товаров
    
    // ID инфоблоков, перебор всех инфоблоков убрал, иначе все падает из-за нагрузки
    private $productIblocks = [26, 27]; // 26 - каталог, 27 - торговые предложения
    
    /**
     * Генерируем прайс-лист всех товаров
     */
    public function generateAndSave()
    {
        Loader::includeModule('iblock');
        Loader::includeModule('catalog');
        
        // Получаем товары из указанных инфоблоков
        $products = $this->getProductsFromSpecificIblocks();
        
        // Формируем PDF
        $html = $this->generateHtml($products);
        
        // Настройки Dompdf
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('chroot', $_SERVER['DOCUMENT_ROOT']);
        
        $dompdf = new Dompdf($options);
        
        $context = stream_context_create([
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
        ]);
        $dompdf->setHttpContext($context);
        
        // Генерируем PDF
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Сохраняем файл
        $fileName = "price-list-" . date('Y-m-d-His') . ".pdf";
        $savePath = $_SERVER['DOCUMENT_ROOT'] . "/upload/managers/price/{$fileName}";
        
        // Создаем директорию
        $dirPath = $_SERVER['DOCUMENT_ROOT'] . "/upload/managers/price/";
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
        }
        
        file_put_contents($savePath, $dompdf->output());
        
        return [
            'success' => true,
            'file_path' => $savePath,
            'file_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/upload/managers/price/' . $fileName,
            'file_name' => $fileName,
            'count' => count($products)
        ];
    }
    
    /**
     * Получаем товары
     */
    private function getProductsFromSpecificIblocks()
    {
        $products = [];
        $counter = 0;
        $page = 0;
        $pageSize = 100;
        
        do {
            $pageProducts = $this->getProductsPage($this->productIblocks, $page, $pageSize);
            
            foreach ($pageProducts as $product) {
                // Пропускаем товары с количеством = 0
                if ($product['quantity'] <= 0) {
                    continue;
                }
                
                // Пропускаем товары без цены
                if ($product['price'] <= 0) {
                    continue;
                }
                
                $products[] = $product;
                $counter++;
                
                // Ограничиваем общее количество
                if ($counter >= $this->limit) {
                    break 2;
                }
            }
            
            $page++;
            
        } while (!empty($pageProducts) && $counter < $this->limit);
        
        return $products;
    }
    
    /**
     * Получает страницу товаров
     */
    private function getProductsPage($iblockIds, $page, $pageSize)
    {
        $offset = $page * $pageSize;
        $products = [];
        
        // Получаем элементы с пагинацией
        $res = \CIBlockElement::GetList(
            ['NAME' => 'ASC'],
            [
                'IBLOCK_ID' => $iblockIds,
                'ACTIVE' => 'Y',
            ],
            false,
            ['nTopCount' => $pageSize, 'nOffset' => $offset],
            ['ID', 'NAME', 'IBLOCK_ID']
        );
        
        while ($element = $res->Fetch()) {
            $productId = $element['ID'];
            
            // Количество
            $quantity = $this->getProductQuantity($productId);
            
            // Цена
            $price = $this->getProductPrice($productId);
            
            $products[] = [
                'id' => $productId,
                'name' => $element['NAME'],
                'price' => $price,
                'quantity' => $quantity,
                'iblock_id' => $element['IBLOCK_ID']
            ];
        }
        
        return $products;
    }
    
    /**
     * Получает цену товара
     */
    private function getProductPrice($productId)
    {
        // Получаем базовую цену (CATALOG_GROUP_ID = 1)
        $price = \Bitrix\Catalog\PriceTable::getList([
            'filter' => [
                'PRODUCT_ID' => $productId,
                'CATALOG_GROUP_ID' => 1,
            ],
            'select' => ['PRICE'],
        ])->fetch();
        
        if ($price) {
            return (float)$price['PRICE'];
        }
        
        // Если нет базовой, берем следующую активную, на всякий случай
        $price = \Bitrix\Catalog\PriceTable::getList([
            'filter' => ['PRODUCT_ID' => $productId],
            'select' => ['PRICE'],
            'limit' => 1
        ])->fetch();
        
        return $price ? (float)$price['PRICE'] : 0;
    }
    
    /**
     * Получаем количество товара
     */
    private function getProductQuantity($productId)
    {
        $product = \Bitrix\Catalog\ProductTable::getList([
            'filter' => ['ID' => $productId],
            'select' => ['QUANTITY', 'AVAILABLE'],
        ])->fetch();
        
        if (!$product) {
            return 0;
        }
        
        // Если товар недоступен
        if ($product['AVAILABLE'] == 'N') {
            return 0;
        }
        
        return (float)$product['QUANTITY'];
    }
    
    /**
     * Генерируем HTML
     */
    private function generateHtml($products)
    {
        ob_start();
        ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9px;
            line-height: 1.1;
            margin: 0;
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header img {
            height: 35px;
        }
        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <p><strong>ООО "РАБОЧАЯ ОДЕЖДА"</strong></p>
        <p><img src="https://<?= $_SERVER['HTTP_HOST'] ?>/upload/managers/logo/logo.png" /></p>
        <p>г. Ставрополь, пр-кт Кулакова, д. 34а</p>
        <h4>Прайс-лист от <?= date('d.m.Y') ?></h4>
    </div>
    
    <?php if (empty($products)): ?>
        <div style="text-align:center; padding: 40px; color: red;">
            <strong>Товары в наличии не найдены!</strong><br>
            Нет товаров с количеством > 0.
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th width="30">№</th>
                    <th>Наименование товара</th>
                    <th width="60">Кол-во</th>
                    <th width="70">Цена, руб.</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $index => $product): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= number_format($product['quantity'], 0, '', ' ') ?></td>
                    <td><?= number_format($product['price'], 2, '.', ' ') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="footer">
            <p>Всего товаров: <?= count($products) ?></p>
            <p>Сгенерировано: <?= date('d.m.Y H:i:s') ?></p>
        </div>
    <?php endif; ?>
</body>
</html>
        <?php
        return ob_get_clean();
    }
}