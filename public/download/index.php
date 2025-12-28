<?php
declare(strict_types=1);

ini_set("error_reporting", E_ALL & ~E_DEPRECATED);

require __DIR__ . '/../../composer/vendor/autoload.php';

use Zibrov\OtusPhp2025\Elastic\ElasticClient;
use Zibrov\OtusPhp2025\Elastic\ElasticDownload;
use Zibrov\OtusPhp2025\Information;

?>

    <p><-- <a href="/">назад</a></p>

    <p>Загружаем данные в Elasticsearch.</p>

<?php
$filename = $_SERVER['DOCUMENT_ROOT'] . Information::FILE_PATH_BOOKS_JSON;

$elasticClient = new ElasticClient();
$elasticIndexer = new ElasticDownload($elasticClient);

try {
    $elasticIndexer->loadData($filename, Information::NAME_ELASTIC_INDEX);
} catch (JsonException $e) {
    echo 'Ошибка в Elasticsearch JSON: ' . $e->getMessage();
}
