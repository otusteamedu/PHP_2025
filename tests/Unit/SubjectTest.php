<?php

namespace Tests\Unit;

use Elisad5791\Phpapp\ElasticSearchClientInterface;
use Elisad5791\Phpapp\Subject;
use Tests\Support\UnitTester;

class SubjectTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;
    private $elasticMock;

    public function _before()
    {
        $this->elasticMock = $this->makeEmpty(ElasticSearchClientInterface::class);
    }

    public function testGetById()
    {
        $expected = ['_source' => ['id' => '1', 'title' => 'Science']];
        
        $this->elasticMock->method('get')
            ->with(['index' => Subject::INDEX_NAME, 'id' => '1'])
            ->willReturn($expected);
            
        $subject = new Subject($this->elasticMock);
        $result = $subject->getById('1');
        
        $this->assertEquals($expected, $result);
    }

    public function testAdd()
    {
        $data = [
            [
                'id' => '1', 
                'title' => 'Art', 
                'book_count' => 1000, 
                'url' => 'https://openlibrary.org/subjects/art.json',
            ],
            [
                'id' => '2', 
                'title' => 'History', 
                'book_count' => 1500, 
                'url' => 'https://openlibrary.org/subjects/history.json',
            ]
        ];
        
        $this->elasticMock->expects($this->once())
            ->method('bulk')
            ->with($this->callback(function($params) {
                return count($params['body']) === 4;
            }));
            
        $subject = new Subject($this->elasticMock);
        $subject->add($data);
    }

    public function testDelete()
    {
        $this->elasticMock->expects($this->once())
            ->method('delete')
            ->with(['index' => Subject::INDEX_NAME, 'id' => '1']);
            
        $subject = new Subject($this->elasticMock);
        $subject->delete('1');
    }
}
