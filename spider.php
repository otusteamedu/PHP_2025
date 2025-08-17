<?php

use Elastic\Elasticsearch\ClientBuilder;
use Elisad5791\Phpapp\Book;
use Elisad5791\Phpapp\ElasticsearchWrapper;
use Elisad5791\Phpapp\NativeHttpClient;
use Elisad5791\Phpapp\Subject;

set_time_limit(0);
ini_set('memory_limit', '-1');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);

use Elisad5791\Phpapp\OpenLibrary;

require_once 'vendor/autoload.php';

$subjectNames = [
    'art',
    'science_fiction',
    'fantasy',
    'biographies',
    'recipes',
    'romance',
    'textbooks',
    'children',
    'history',
    'medicine',
    'religion',
    'mystery_and_detective_stories',
    'plays',
    'music',
    'science',
];

try {
    $client = ClientBuilder::create()->setHosts(['http://localhost:9200'])->build();
    $clientWrapper = new ElasticsearchWrapper($client);
    
    $params = ['index' => 'subjects'];
    $client->indices()->create($params);
    $params = ['index' => 'books'];
    $client->indices()->create($params);
    
    $library = new OpenLibrary(new NativeHttpClient());
    $subjectHandler = new Subject($clientWrapper);
    $bookHandler = new Book($clientWrapper);

    foreach ($subjectNames as $key => $subject) {
        $result = $library->getSubject($subject);
        echo 'Получена тема' . PHP_EOL;
        echo 'Начато уточнение данных книг...' . PHP_EOL;

        $subject = $result['subject'];

        $items = $result['books'];
        $bookCounter = 0;
        $books = [];
        foreach ($items as $book) {
            $bookId = $book['id'];

            $description = $library->getDescription($bookId);
            $rating = $library->getRating($bookId);
            $pageCount = $library->getPageCount($bookId);

            $book['page_count'] = $pageCount;
            $book['rating'] = $rating;
            $book['description'] = $description;

            echo $bookId . ' (subject ' . $key . ', book ' . $bookCounter . ') - done' . PHP_EOL;
            $bookCounter++;

            $books[] = $book;
        }

        $subjectHandler->add([$subject]);
        $bookHandler->add($books);

        echo $subject['title'] . ' (subject ' . $key . ') - done' . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Ошибка: ", $e->getMessage(), "\n";
}
