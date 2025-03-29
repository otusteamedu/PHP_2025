<?php declare(strict_types=1);

use App\App;

require(__DIR__ . '/../vendor/autoload.php');

$client = Elastic\Elasticsearch\ClientBuilder::create()->build();

$app = new App($argv);
$response = $app->run();

echo $response;
