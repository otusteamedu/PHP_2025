<?php
namespace Aovchinnikova\Hw15\Tests\Unit\Model;

use Aovchinnikova\Hw15\Model\ValidationResult;
use PHPUnit\Framework\TestCase;

class ValidationResultTest extends TestCase
{
    public function testGetters()
    {
        $result = new ValidationResult('test@example.com', true, true);
        
        $this->assertEquals('test@example.com', $result->getEmail());
        $this->assertTrue($result->isValidFormat());
        $this->assertTrue($result->hasValidDNS());
    }

    public function testToArray()
    {
        $result = new ValidationResult('test@example.com', true, false);
        $array = $result->toArray();
        
        $this->assertEquals([
            'email' => 'test@example.com',
            'isValid' => false,
            'details' => [
                'format' => true,
                'dns' => false
            ]
        ], $array);
    }
}