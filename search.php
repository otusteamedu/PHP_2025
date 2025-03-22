<?php

require_once 'сurl_request.php';

function performSearch() {
    echo "Введите название книги (часть названия): ";
    $title = trim(fgets(STDIN));

    echo "Выберите магазин (Ленина, Мира): ";
    $shop = trim(fgets(STDIN));
    if (!in_array($shop, ['Ленина', 'Мира'])) {
        die("Неверный магазин. Допустимые значения: Ленина, Мира.");
    }

    echo "Цена не более: ";
    $price = intval(trim(fgets(STDIN)));

    echo "Необходимое количество книг: ";
    $stock = intval(trim(fgets(STDIN)));

    $query = [
        "query" => [
            "bool" => [
                "must" => [
                    [
                        "match" => [
                            "title" => [
                                "query" => $title,
                                "fuzziness" => "2"
                            ]
                        ]
                    ],
                    [
                        "nested" => [
                            "path" => "stock",
                            "query" => [
                                "bool" => [
                                    "must" => [
                                        [
                                            "match" => [
                                                "stock.shop" => $shop
                                            ]
                                        ],
                                        [
                                            "range" => [
                                                "stock.stock" => [
                                                    "gte" => $stock
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "range" => [
                            "price" => [
                                "lte" => $price
                            ]
                        ]
                    ]
                ]
            ]
        ],
        "sort" => [
            [
                "price" => [
                    "order" => "asc"
                ]
            ]
        ]
    ];

    $jsonQuery = json_encode($query, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    $url = "https://localhost:9200/otus-shop/_search";

    $username = 'elastic';
    $password = 'Kq1kw1A42SOg_VrTd3Ip';
    $auth = base64_encode("$username:$password");

    $headers = [
        'Authorization: Basic ' . $auth,
        'Content-Type: application/json'
    ];

    $response = curlRequest($url, 'GET', $headers, $jsonQuery);

    $responseData = json_decode($response, true);

    if (isset($responseData['hits']['total']['value']) && $responseData['hits']['total']['value'] > 0) {
        echo "\nРезультаты поиска:\n";
        print_r($responseData['hits']['hits']);
    } else {
        echo "\nПоиск не дал результатов. Попробуйте изменить параметры.\n";
        echo "Хотите попробовать снова? (да/нет): ";
        $retry = trim(fgets(STDIN));
        if (strtolower($retry) === 'да') {
            performSearch();
        } else {
            echo "Поиск завершён.\n";
        }
    }
}

performSearch();