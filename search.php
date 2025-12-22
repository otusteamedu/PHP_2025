<?php
require_once 'ElasticsearchLib.php';

try {
    $obElasticsearch = new ElasticsearchLib();
    
    $obElasticsearch->checkIndex();

    $arOptions = getopt('n:c:p:u');
    
    $sQuery = '';

    if (isset($arOptions['n'])) {
        $sQuery = $arOptions['n'];
    } 
    if (empty($sQuery)) {
        die("Укажите поисковый запрос: php search.php -n 'рыцОри'\n");
    }

    $sCategory = null;
    if (isset($arOptions['c'])) {
        $sCategory = $arOptions['c'];
    } 

    $iMaxPrice = 2000;
    if (isset($arOptions['p'])) {
        $iMaxPrice = $arOptions['p'];
    } 

    $bHideUnavailable = true;
    if (array_key_exists ("u",$arOptions)) {
          $bHideUnavailable = false;        
    }
       
    $iMaxPrice = (float)$iMaxPrice;
    
    $arResults = $obElasticsearch->searchBooks($sQuery, $sCategory, $iMaxPrice, $bHideUnavailable);
    if (empty($arResults)) {
        echo "По запросу '{$sQuery}' ничего не найдено\n";
        exit;
    }
    $iMaxTitle = 40;
    $iMaxCategory = 25;
    
    echo "\n" . str_pad('Название', $iMaxTitle) . " " .
         str_pad('Категория', $iMaxCategory) . " " .
         str_pad('Цена', 10) . " " .
         str_pad('В наличии', 12) . "\n"; 
    
    echo str_repeat('-', $iMaxTitle + $iMaxCategory + 40) . "\n";
    
    foreach ($arResults as $arBook) {
        $sTitle = mb_strlen($arBook['title']) > $iMaxTitle - 2 
            ? mb_substr($arBook['title'], 0, $iMaxTitle - 3) . '...' 
            : $arBook['title'];
        
        $sCategory = mb_strlen($arBook['category']) > $iMaxCategory - 2
            ? mb_substr($arBook['category'], 0, $iMaxCategory - 3) . '...'
            : $arBook['category'];
        
        $sStock = $arBook['stock'] > 0 ? 'Да (' . $arBook['stock'] . ')' : 'Нет';
        
        echo str_pad($sTitle, $iMaxTitle) . " " .
             str_pad($sCategory, $iMaxCategory) . " " .
             str_pad(number_format($arBook['price'], 2), 10) . " " .
             str_pad($sStock, 12) .  "\n";
    }
    
    echo "\nНайдено книг: " . count($arResults) . "\n";
    
} catch (Exception $e) {
    die("Ошибка: " . $e->getMessage() . "\n");
}