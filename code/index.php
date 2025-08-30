<?php

declare(strict_types=1);

require_once 'CheckBracket.php';

use Dkeruntu\OtusHomeWorkFourCheckBracket\CheckBracket;


echo "<h1>Информация по контейнерам</h1>";

$sBalancerContainer = $_SERVER['HTTP_X_BALANCER_CONTAINER'] ?? 'Not set';
$sBackendContainer = $_SERVER['HTTP_X_BACKEND_CONTAINER'] ?? 'Not set';

echo "<p>Балансировщик: <strong>$sBalancerContainer</strong></p>";
echo "<p>Бекенл: <strong>$sBackendContainer</strong></p>";

echo "<h1>Информация по обработке</h1>";

$sString = $_GET['string'] ?? $_POST['string'] ?? null;


if ($sString !== null && $sString !== '') {
    echo "На вход поступило: " . htmlspecialchars($sString) . "<br>";
    echo "Метод: " . ($_GET['string'] ? 'GET' : 'POST') . "<br>";
}else{
    $arRes["bError"] = true;
    $arRes["bErbErrorDescriptionror"] = "На вход не поступило параметра string";
}

if (!$arRes["bError"]){
    $obChekBracket = new CheckBracket();

    $arParityResult = $obChekBracket->ChekcParity($sString);

    if (!$arParityResult["bError"]){
        $arRes = $obChekBracket->CheckValidation($sString,$arParityResult["iCharQuantity"]);
    }else{
    $arRes = $arParityResult;
    };
}

if ($arRes["bError"]){
    echo "Обнаружены ошибки<br>";
    echo '<pre>';
    print_r($arRes);
    echo '</pre>';
}else{
    echo "Все окей";
}
