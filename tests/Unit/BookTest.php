<?php

namespace Tests\Unit;

use Elisad5791\Phpapp\Book;
use Elisad5791\Phpapp\ElasticSearchClientInterface;
use Tests\Support\UnitTester;

class BookTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;
    private $elasticMock;

    public function _before()
    {
        $this->elasticMock = $this->makeEmpty(ElasticSearchClientInterface::class);
    }

    public function testGetById()
    {
        $expected = ['_source' => ['id' => '1', 'title' => 'Physics']];
        
        $this->elasticMock
            ->method('get')
            ->with(['index' => Book::INDEX_NAME, 'id' => '1'])
            ->willReturn($expected);
            
        $book = new Book($this->elasticMock);
        $result = $book->getById('1');
        
        $this->assertEquals($expected, $result);
    }

    public function testGetBySubjectId()
    {
        $expected = ['hits' => ['hits' => [
            ['_source' => ['id' => '1', 'title' => 'Physics', 'subject_id' => 'science']],
            ['_source' => ['id' => '2', 'title' => 'Chemistry', 'subject_id' => 'science']]
        ]]];
        
        $this->elasticMock->expects($this->once())
            ->method('search')
            ->with([
                'index' => Book::INDEX_NAME,
                'body' => [
                    'query' => [
                        'match' => [
                            'subject_id' => 'science'
                        ]
                    ]
                ]
            ])
            ->willReturn($expected);
            
        $book = new Book($this->elasticMock);
        $result = $book->getBySubjectId('science');
        
        $this->assertEquals($expected, $result);
    }

    public function testSearchByTitle()
    {
        $expected = ['hits' => ['hits' => [
            ['_source' => ['id' => '1', 'title' => 'Quantum Physics']]
        ]]];
        
        $this->elasticMock->expects($this->once())
            ->method('search')
            ->with([
                'index' => Book::INDEX_NAME,
                'body' => [
                    'query' => [
                        'match' => [
                            'title' => 'quantum'
                        ]
                    ]
                ]
            ])
            ->willReturn($expected);
            
        $book = new Book($this->elasticMock);
        $result = $book->searchByTitle('quantum');
        
        $this->assertEquals($expected, $result);
    }

    public function testSearchByDescription()
    {
        $expected = ['hits' => ['hits' => [
            ['_source' => ['id' => '1', 'title' => 'Quantum Physics', 'description' => 'History and modern state of quantum physics']]
        ]]];
        
        $this->elasticMock->expects($this->once())
            ->method('search')
            ->with([
                'index' => Book::INDEX_NAME,
                'body' => [
                    'query' => [
                        'match' => [
                            'description' => 'quantum physics'
                        ]
                    ]
                ]
            ])
            ->willReturn($expected);
            
        $book = new Book($this->elasticMock);
        $result = $book->searchByDescription('quantum physics');
        
        $this->assertEquals($expected, $result);
    }

    public function testAdd()
    {
        $data = [
            [
                'id' => '/works/AAAA', 
                'subject_id' => 'science', 
                'title' => 'Physics for kids', 
                'author' => 'Emil Wolf', 
                'page_count' => 413
            ],
            [
                'id' => '/works/BBBB',  
                'subject_id' => 'science', 
                'title' => 'Progress in optics', 
                'author' => 'E. Brambilla', 
                'page_count' => 356
            ],
        ];
        
        $this->elasticMock->expects($this->once())
            ->method('bulk')
            ->with($this->callback(function($params) {
                return count($params['body']) === 4;
            }));
            
        $book = new Book($this->elasticMock);
        $book->add($data);
    }

    public function testDelete()
    {
        $this->elasticMock->expects($this->once())
            ->method('delete')
            ->with(['index' => Book::INDEX_NAME, 'id' => '1']);
            
        $subject = new Book($this->elasticMock);
        $subject->delete('1');
    }
}