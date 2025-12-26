<?php
require __DIR__ . '/vendor/autoload.php';
use Ak\Hw\Models\Elastic;
use Ak\Hw\Models\Shop;


$categories = Shop::getCategories();
$categoriesStr = '';
foreach ($categories as $key => $value) {
    $categoriesStr .= "$key - $value\n";
}

if (extension_loaded('readline')) {
    echo "Добро пожаловать! Давайте найдем Вам книгу.\n";

    $category = readline("Шаг 1:Выберите категорию: .\n ".$categoriesStr);

    $price = readline("До какой цены вам нужна книга?\n");

    // Шаг 1: Ввод имени
    $title = readline("Вы можете найти что нибудь по названию:\n ");

    $result = Shop::find($categories[$category], $price, $title);

    if(!$result){
        echo "к сожалению нам не удалось найти Вам книгу \n" ;
        echo "Скрипт завершен.\n";
        exit();
    }


    echo "Вот что мы нашли для Вас:\n";
    echo "--------------------------------------------------\n";
    echo sprintf("| %-30s | %-15s | %-10s |\n", "Название", "Категория", "Цена");
    echo "--------------------------------------------------\n";
    foreach ($result as $book) {
        echo sprintf("| %-30s | %-15s | %-10s |\n", $book["_source"]['title'], $book["_source"]['category'], $book["_source"]['price']);
    }
    echo "--------------------------------------------------\n";
}

echo "Скрипт завершен.\n";