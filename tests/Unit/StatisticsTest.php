<?php

namespace Tests\Unit;

use Elisad5791\Phpapp\ElasticSearchClientInterface;
use Elisad5791\Phpapp\Statistics;
use Tests\Support\UnitTester;

class StatisticsTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;
    private $elasticMock;

    public function _before()
    {
        $this->elasticMock = $this->makeEmpty(ElasticSearchClientInterface::class);
    }

    public function testGetAverageRatingBySubject()
    {
        $query = [
            'index' => Statistics::INDEX_NAME,
            'body' => [
                'aggs' => [
                    'subjects' => [
                        'terms' => ['field' => 'subject_id.keyword', 'size' => 1000],
                        'aggs' => ['avg_rating' => ['avg' => ['field' => 'rating']]]
                    ]
                ]
            ]
        ];

        $expected = [
            'aggregations' => [
                'subjects' => [
                    'buckets' => [
                        ['key' => 'sci-fi', 'doc_count' => 10, 'avg_rating' => ['value' => 4.2]],
                        ['key' => 'fantasy', 'doc_count' => 8, 'avg_rating' => ['value' => 4.5]]
                    ]
                ]
            ]
        ];

        $this->elasticMock->expects($this->once())
            ->method('search')
            ->with($query)
            ->willReturn($expected);

        $stats = new Statistics($this->elasticMock);
        $result = $stats->getAverageRatingBySubject();

        $this->assertArrayHasKey('aggregations', $result);
        $this->assertArrayHasKey('subjects', $result['aggregations']);
        $this->assertArrayHasKey('buckets', $result['aggregations']['subjects']);
        $this->assertCount(2, $result['aggregations']['subjects']['buckets']);
        $this->assertEquals(4.2, $result['aggregations']['subjects']['buckets'][0]['avg_rating']['value']);
        $this->assertEquals(8, $result['aggregations']['subjects']['buckets'][1]['doc_count']);
    }

    public function testGetLongestBooks()
    {
        $limit = 3;
        $query = [
            'index' => Statistics::INDEX_NAME,
            'body' => [
                'sort' => [['page_count' => ['order' => 'desc']]],
                'size' => $limit
            ]
        ];

        $expected = [
            'hits' => [
                'hits' => [
                    ['_source' => ['title' => 'Book A', 'page_count' => 1000]],
                    ['_source' => ['title' => 'Book B', 'page_count' => 800]],
                    ['_source' => ['title' => 'Book C', 'page_count' => 700]]
                ]
            ]
        ];

        $this->elasticMock->expects($this->once())
            ->method('search')
            ->with($query)
            ->willReturn($expected);

        $stats = new Statistics($this->elasticMock);
        $result = $stats->getLongestBooks($limit);

        $this->assertArrayHasKey('hits', $result);
        $this->assertArrayHasKey('hits', $result['hits']);
        $this->assertCount(3, $result['hits']['hits']);
        $this->assertEquals('Book A', $result['hits']['hits'][0]['_source']['title']);
        $this->assertEquals(700, $result['hits']['hits'][2]['_source']['page_count']);
    }
}