<?php

namespace Tests\Unit;

use Elastic\Elasticsearch\ClientBuilder;
use Elisad5791\Phpapp\Book;
use Elisad5791\Phpapp\ElasticsearchWrapper;
use Elisad5791\Phpapp\Statistics;
use Elisad5791\Phpapp\Subject;
use Tests\Support\UnitTester;
use Exception;

class IntegrationTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;
    private $clientWrapper;

    public function _before()
    {
        $client = ClientBuilder ::create()->setHosts(['http://localhost:9200'])->build();
        $this->clientWrapper = new ElasticsearchWrapper($client);
    }

    public function testSubjectCrudOperations()
    {
        $subject = new Subject($this->clientWrapper);
        $testData = [
            ['id' => 'subj1', 'title' => 'plants', 'book_count' => 450],
            ['id' => 'subj2', 'title' => 'animals', 'book_count' => 560]
        ];
        $subject->add($testData);

        $result = $subject->getById('subj1');
        $this->assertEquals('plants', $result['_source']['title']);
        $result = $subject->getById('subj2');
        $this->assertEquals(560, $result['_source']['book_count']);

        $subject->delete('subj2');
        $this->expectException(Exception::class);
        $subject->getById('subj2');
    }

    public function testBookCrudOperations()
    {
        $book = new Book($this->clientWrapper);
        $testData = [
            [
                'id' => '/works/AAAA', 
                'subject_id' => 'subj1', 
                'title' => 'Physics for kids', 
                'author' => 'Emil Wolf', 
                'page_count' => 413
            ],
            [
                'id' => '/works/BBBB',  
                'subject_id' => 'subj1', 
                'title' => 'Progress in optics', 
                'author' => 'E. Brambilla', 
                'page_count' => 356
            ],
        ];
        $book->add($testData);

        $result = $book->getById('/works/AAAA');
        $this->assertEquals('Physics for kids', $result['_source']['title']);
        $this->assertEquals(413, $result['_source']['page_count']);
        $result = $book->getById('/works/BBBB');
        $this->assertEquals('Progress in optics', $result['_source']['title']);
        $this->assertEquals('E. Brambilla', $result['_source']['author']);
     
        $book->delete('/works/BBBB');
        $this->expectException(Exception::class);
        $book->getById('/works/BBBB');
    }

    public function testBookSearchOperations()
    {
        $book = new Book($this->clientWrapper);
        $testData = [
            [
                'id' => '/works/book1',
                'title' => 'Dune',
                'subject_id' => 'subj1',
                'rating' => 4.5,
                'page_count' => 412,
                'description' => 'Epic2025 sci-fi novel'
            ],
            [
                'id' => '/works/book2',
                'title' => 'The Hobbit2025',
                'subject_id' => 'subj1',
                'rating' => 4.8,
                'page_count' => 310,
                'description' => 'Fantasy adventure'
            ]
        ];
        $book->add($testData);

        $result = $book->searchByTitle('Hobbit2025');
        $this->assertEquals('The Hobbit2025', $result['hits']['hits'][0]['_source']['title']);

        $result = $book->searchByDescription('epic2025');
        $this->assertEquals('Dune', $result['hits']['hits'][0]['_source']['title']);
    }

    public function testStatisticsMethods()
    {
        $book = new Book($this->clientWrapper);
        $books = [
            [
                'id' => '/works/book11',
                'title' => 'Book A',
                'subject_id' => 'subj123',
                'rating' => 4.0,
                'page_count' => 10000000
            ],
            [
                'id' => '/works/book22',
                'title' => 'Book B',
                'subject_id' => 'subj123',
                'rating' => 4.4,
                'page_count' => 20000000
            ],
            [
                'id' => '/works/book33',
                'title' => 'Book C',
                'subject_id' => 'subj123',
                'rating' => 4.8,
                'page_count' => 30000000
            ]
        ];
        $book->add($books);
        sleep(1);

        $stats = new Statistics($this->clientWrapper);

        /*$result = $stats->getAverageRatingBySubject();

        $buckets = $result['aggregations']['subjects']['buckets'];
        $ratingData = array_values(array_filter($buckets, function($item) {
            return $item['key'] == 'subj123';
        }));
        $rating = $ratingData[0]['avg_rating']['value'];
        $this->assertEqualsWithDelta(4.4, $rating, 0.01);*/

        $result = $stats->getLongestBooks(2);

        $this->assertCount(2, $result['hits']['hits']);
        $this->assertEquals(30000000, $result['hits']['hits'][0]['_source']['page_count']);
        $this->assertEquals(20000000, $result['hits']['hits'][1]['_source']['page_count']);
    }
}