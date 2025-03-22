<?php

require_once 'сurl_request.php';

$index = 'otus_shop_index.json';
$content = 'otus_shop_content.json';

if (!file_exists($index)) {
    die("Файл $index не найден.");
}

if (!file_exists($content)) {
    die("Файл $content не найден.");
}

$username = 'elastic';
$password = 'Kq1kw1A42SOg_VrTd3Ip';
$auth = base64_encode("$username:$password");

$headers = [
    'Authorization: Basic ' . $auth,
    'Content-Type: application/json'
];
$response = curlRequest('https://localhost:9200/otus-shop', 'PUT', $headers, file_get_contents($index));

$responseData = json_decode($response, true);
if (isset($responseData['acknowledged']) && $responseData['acknowledged'] === true) {
    echo "Индекс 'otus-shop' успешно создан.\n";
} else {
    die("Ошибка при создании индекса: " . $response);
}

$response = curlRequest('https://localhost:9200/_bulk', 'POST', $headers, file_get_contents($content));

$responseData = json_decode($response, true);
if (isset($responseData['errors']) && $responseData['errors'] === false) {
    echo "Данные успешно загружены.\n";
} else {
    echo "Ошибки при загрузке данных:\n";
    print_r($responseData);
}

