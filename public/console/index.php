<?php
declare(strict_types=1);

ini_set("error_reporting", E_ALL & ~E_DEPRECATED);

require __DIR__ . '/../../composer/vendor/autoload.php';

use Zibrov\OtusPhp2025\Elastic\ElasticClient;
use Zibrov\OtusPhp2025\Elastic\ElasticDownload;
use Zibrov\OtusPhp2025\Elastic\ElasticIndexer;
use Zibrov\OtusPhp2025\Elastic\ElasticSearcher;
use Zibrov\OtusPhp2025\Information;

?>

    <p>Работа консоли "Elasticsearch" описана в файле README.md</p>

<?php
// php index.php --action="create"
// php index.php --action="create" --index="otus-shop-new"

// php index.php --action="download"
// php index.php --action="download" --file="/upload/books.json"

// php index.php --action="search" --title="рыцОри" --price="2000"
// php index.php --action="search" --title="рыцОри" --price="2000" --index="otus-shop-new"

// php index.php --action="remove"
// php index.php --action="remove" --index="otus-shop-new"

if ($arParams = (getopt('', ['action:', 'index::', 'file::', 'title::', 'price::']))) {
    $elasticClient = new ElasticClient('localhost');

    if ($arParams['action'] === 'create') {

        $index = $arParams['index'] ?? Information::NAME_ELASTIC_INDEX;

        $elasticIndexer = new ElasticIndexer($elasticClient);
        $elasticIndexer->createIndex($index);

    } else if ($arParams['action'] === 'download') {

        $filename = __DIR__ . '/..' . ($arParams['file'] ?? Information::FILE_PATH_BOOKS_JSON);

        $elasticIndexer = new ElasticDownload($elasticClient);
        try {
            $elasticIndexer->loadData($filename, Information::NAME_ELASTIC_INDEX);
        } catch (JsonException $e) {
            echo 'Ошибка в Elasticsearch JSON: ' . $e->getMessage();
        }

    } else if ($arParams['action'] === 'search') {

        $title = $arParams['title'] ?? '';
        $price = (float)($arParams['price'] ?? 0);
        $index = $arParams['index'] ?? Information::NAME_ELASTIC_INDEX;

        $elasticSearcher = new ElasticSearcher($elasticClient);
        $elasticSearcher->search($title, $price, $index);

    } else if ($arParams['action'] === 'remove') {

        $index = $arParams['index'] ?? Information::NAME_ELASTIC_INDEX;

        $elasticIndexer = new ElasticIndexer($elasticClient);
        $elasticIndexer->removeIndex($index);
    }
}
