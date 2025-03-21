<?php

//require_once 'autoload.php';
//
//use classes\App;
//
//try {
//    $app = new App();
//    echo '<pre>';
//    var_dump($app->run());
//    echo '</pre>';
//}
//catch (Exception $e) {
//     print_r($e->getMessage());
//}
//

echo 'test';

require '../vendor/autoload.php';

$client = Elastic\Elasticsearch\ClientBuilder::create()->build();

$params = ['body' => []];

for ($i = 100; $i <= 110; $i++) {
    $params['body'][] = [
        'index' => [
            '_index' => 'my_new_index',
            '_id'    => $i
        ]
    ];

    $params['body'][] = [
        'title'     => 'test'.$i,
        'second_field' => 'some more values'
    ];
}

// Send the last batch if it exists
if (!empty($params['body'])) {
    $responses = $client->bulk($params);
}
