<?php
declare(strict_types=1);

require __DIR__ . '/../../../composer/vendor/autoload.php';

use Zibrov\OtusPhp2025\Application;
use Zibrov\OtusPhp2025\Database\Postgresql\Config as PostgresqlConfig;

?>

<p><-- <a href="/postgresql/">назад</a></p>

<p>Выполняем операции с Postgres</p>

<?php
$obPostgresqlConfig = new PostgresqlConfig();
$app = new Application($obPostgresqlConfig);
?>

<p>Создание категорий!</p>
<?php
$arCategory = [
    [
        'name' => 'Книги',
        'code' => 'books'
    ],
    [
        'name' => 'Ежедневные газеты',
        'code' => 'magazines'
    ],
    [
        'name' => 'Дневники',
        'code' => 'diaries'
    ],
];
foreach ($arCategory as $arItem) {
    $app->createCategory($arItem);
    echo '<br>';
}
?>

<p>Получение всех категорий:</p>
<?php
$arCategory = $app->getAllCategory();
?>

<p>Создание предложений!</p>
<?php
$arOffer = [
    [
        'name' => 'Интересные факты',
        'color' => 'белый',
        'price' => 215, 50,
    ],
    [
        'name' => 'Красная книга',
        'color' => 'красная',
        'price' => 199, 00,
    ],
    [
        'name' => 'Мурзилка',
        'color' => 'желтый',
        'price' => 99, 99,
    ],
];
if ($arCategoryId = array_column($arCategory, 'id')) {
    foreach ($arOffer as $arItem) {
        $arItem['category_id'] = $arCategoryId[array_rand($arCategoryId)];
        $app->createOffers($arItem);
        echo '<br>';
    }
}
?>

<p>Получение всех предложения:</p>
<?php
$arOffers = $app->getAllOffers();
?>

<p>Изменение категории:</p>
<?php
foreach ($arCategory as $arItemCategory) {
    if ($arItemCategory['code'] === 'magazines') {
        $arItemCategory['old_name'] = $arItemCategory['name'];
        $arItemCategory['name'] = 'Журналы';

        $app->updatedCategory($arItemCategory);
    }
}
?>

<p>Изменение предложения:</p>
<?php
foreach ($arOffers as $arItemOffers) {
    if ($arItemOffers['name'] === 'Мурзилка') {
        $arItemOffers['old_price'] = $arItemOffers['price'];
        $arItemOffers['price'] = '101.99';

        $app->updatedOffers($arItemOffers);
    }
}
?>

<p>Удаление предложения:</p>
<?php
$categoryId = 0;
$arOffersSortByCategory = [];
foreach ($arOffers as $arItemOffers) {
    $arOffersSortByCategory[$arItemOffers['category_id']][] = $arItemOffers;
}

if (count($arOffersSortByCategory) > 0) {
    foreach ($arOffersSortByCategory as $id => $arOffersByCategory) {
        $categoryId = $id;
        foreach ($arOffersByCategory as $arItemOffers) {
            $app->deleteOffers($arItemOffers['id']);
        }
        break;
    }
}
?>

<p>Удаление категории:</p>
<?php
if (!empty($categoryId)) {
    $app->deleteCategory($categoryId);
}
?>


<p>Получение всех категорий и предложения:</p>
<?php
$arCategory = $app->getAllCategory();
$arOffers = $app->getAllOffers();
?>
