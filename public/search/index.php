<?php
declare(strict_types=1);

ini_set("error_reporting", E_ALL & ~E_DEPRECATED);

require __DIR__ . '/../../composer/vendor/autoload.php';

use Zibrov\OtusPhp2025\Elastic\ElasticClient;
use Zibrov\OtusPhp2025\Elastic\ElasticSearcher;
use Zibrov\OtusPhp2025\Information;

?>

    <p><-- <a href="/">назад</a></p>

    <p>Выполняетмя поиск по наименованию товара и фильтрует результаты по цене</p>

<?php
$elasticClient = new ElasticClient();
$elasticSearcher = new ElasticSearcher($elasticClient);

$title = 'рыцОри';
$price = (float)2000;

$elasticSearcher->search($title, $price, Information::NAME_ELASTIC_INDEX);
