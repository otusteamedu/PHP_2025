<?php

use Elisad5791\Phpapp\ElasticsearchWrapper;
use Elastic\Elasticsearch\ClientBuilder;
use Elisad5791\Phpapp\Book;
use Elisad5791\Phpapp\Subject;
use Elisad5791\Phpapp\Statistics;

error_reporting(E_ALL & ~E_DEPRECATED);

require_once 'vendor/autoload.php';

$client = ClientBuilder::create()->setHosts(['http://localhost:9200'])->build();
$clientWrapper = new ElasticsearchWrapper($client);

// Темы
$subject = new Subject($clientWrapper);
$id = '/subjects/fantasy';
$result = $subject->getById($id);
print_r($result);

// Книги
$book = new Book($clientWrapper);

$id = '/works/OL16996964W';
$result = $book->getById($id);
print_r($result);

$id = '/subjects/fantasy';
$result = $book->getBySubjectId($id);
print_r($result);

$query = 'The Sea Fairies';
$result = $book->searchByTitle($query);
print_r($result);

$query = 'early 1900s';
$result = $book->searchByDescription($query);
print_r($result);

// Статистика
$stat = new Statistics($clientWrapper);

$result = $stat->getAverageRatingBySubject();
print_r($result);

$result = $stat->getLongestBooks();
print_r($result);