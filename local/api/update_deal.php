<?php

define('NO_KEEP_STATISTIC', 'Y');
define("NOT_CHECK_PERMISSIONS", true);
define("CHK_EVENT", true);
$_SERVER["DOCUMENT_ROOT"] = "/var/www/html/bx-site/";
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
CModule::IncludeModule("crm");
use Bitrix\Main\Loader;
use Bitrix\Main\Error;
use Bitrix\Crm\Item;
use Bitrix\Crm\Service\Context;
use Bitrix\Crm\Service\Factory;
use Bitrix\Crm\Service\Operation;
use Bitrix\Main\Result;
use Bitrix\Crm\Service;
use Bitrix\Main\DI;

// Проверяем, что запрос POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// echo json_encode(['error' => 'bitrix_id is required']);
// Получаем bitrix_id из тела запроса
$input = json_decode(file_get_contents('php://input'), true);
$dealId = (int)($input['deal_id'] ?? 0);
if (!$dealId) {
    http_response_code(400);
    echo json_encode(['error' => 'deal_id is required'.print_r($_POST, true)]);
    exit;
}
$count = (int)$input['count'];
if (!$count) {
    http_response_code(400);
    echo json_encode(['error' => 'deal_id is required'.print_r($_POST, true)]);
    exit;
}

try {
    // Получаем фабрику сделок
    $factory = Service\Container::getInstance()->getFactory(\CCrmOwnerType::Deal);
    
    if (!$factory) {
        throw new Exception('Deal factory not found');
    }

    // Создаем фильтр по ответственному пользователю
    // $filter = ['=ASSIGNED_BY_ID' => $bitrixId];
    
    // Получаем элементы через фабрику
    $deal = $factory->getItem($dealId);    
    $deal->set("UF_COUNT_PRODUCT", $count);
    $deal->save();
    $dealData = [
        'ID' => $deal->getId(),
        'TITLE' => $deal->getTitle(),
        'STAGE_ID' => $deal->getStageId(),
        'CURRENCY_ID' => $deal->getCurrencyId(),
        'OPPORTUNITY' => $deal->getOpportunity(),
        'ASSIGNED_BY_ID' => $deal->getAssignedById(),
        // 'CREATED_DATE' => $deal->getCreatedTime()->format(\DateTime::ATOM),
        'UF_PRICE_PRODUCT' => $deal->get('UF_PRICE_PRODUCT') ?? 0,
        'UF_COUNT_PRODUCT' => $deal->get('UF_COUNT_PRODUCT') ?? 0,
    ];
    // Возвращаем JSON
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dealData, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
