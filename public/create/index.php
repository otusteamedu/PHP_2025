<?php
declare(strict_types=1);

ini_set("error_reporting", E_ALL & ~E_DEPRECATED);

require __DIR__ . '/../../composer/vendor/autoload.php';

use Zibrov\OtusPhp2025\Elastic\ElasticClient;
use Zibrov\OtusPhp2025\Elastic\ElasticIndexer;
use Zibrov\OtusPhp2025\Information;

?>

    <p><-- <a href="/">назад</a></p>

    <p>Создаем индекс в Elasticsearch.</p>

<?php
$elasticIndexer = new ElasticIndexer(new ElasticClient());

$elasticIndexer->createIndex(Information::NAME_ELASTIC_INDEX);
