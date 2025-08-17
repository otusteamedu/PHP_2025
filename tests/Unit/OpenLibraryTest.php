<?php

namespace Tests\Unit;

use Elisad5791\Phpapp\HttpClientInterface;
use Elisad5791\Phpapp\OpenLibrary;
use Tests\Support\UnitTester;

class OpenLibraryTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;
    private $httpMock;

    protected function _before()
    {
        $this->httpMock = $this->makeEmpty(HttpClientInterface::class);
    }

    public function testGetSubject() 
    {
        $mockResponse = json_encode([
            'key' => '/subjects/test',
            'name' => 'Test Subject',
            'work_count' => 2,
            'works' => [
                [
                    'key' => '/works/1',
                    'title' => 'Book 1',
                    'authors' => [['name' => 'Author 1']],
                    'first_publish_year' => 2000,
                ],
                [
                    'key' => '/works/2',
                    'title' => 'Book 2',
                    'authors' => [['name' => 'Author 2']],
                    'first_publish_year' => 2010,
                ],
            ],
        ]);

        $this->httpMock->method('get')->willReturn($mockResponse);
        $library = new OpenLibrary($this->httpMock);

        $result = $library->getSubject('test');
        
        $this->assertArrayHasKey('subject', $result);
        $this->assertEquals('/subjects/test', $result['subject']['id']);
        $this->assertEquals('Test Subject', $result['subject']['title']);
        $this->assertEquals(2, $result['subject']['book_count']);

        $this->assertArrayHasKey('books', $result);
        $this->assertCount(2, $result['books']);
        $this->assertEquals('Author 1', $result['books'][0]['author']);
        $this->assertEquals(2.5, $result['books'][0]['rating']);
        $this->assertEquals('Book 2', $result['books'][1]['title']);
        $this->assertEquals(2010, $result['books'][1]['first_publish_year']);
    }

    public function testGetDescription() 
    {
        $mockResponse = json_encode([
            'description' => ['value' => 'Interesting book'],
        ]);

        $this->httpMock->method('get')->willReturn($mockResponse);
        $library = new OpenLibrary($this->httpMock);

        $description = $library->getDescription('/works/1');
        $this->assertEquals('Interesting book', $description);
    }

    public function testGetRating() 
    {
        $mockResponse = json_encode([
            'summary' => ['average' => 4.5],
        ]);

        $this->httpMock->method('get')->willReturn($mockResponse);
        $library = new OpenLibrary($this->httpMock);

        $rating = $library->getRating('/works/1');
        $this->assertEquals(4.5, $rating);
    }

    public function testGetPageCount() 
    {
        $mockResponse = json_encode([
            'entries' => [
                ['number_of_pages' => 250],
            ],
        ]);

        $this->httpMock->method('get')->willReturn($mockResponse);
        $library = new OpenLibrary($this->httpMock);

        $pages = $library->getPageCount('/works/1');
        $this->assertEquals(250, $pages);
    }
}
