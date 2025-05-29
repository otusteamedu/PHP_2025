<?php
namespace Aovchinnikova\Hw15\Tests\Unit\Controller;

use Aovchinnikova\Hw15\Controller\EmailController;
use Aovchinnikova\Hw15\Model\ValidationResult;
use Aovchinnikova\Hw15\Service\EmailValidationService;
use PHPUnit\Framework\TestCase;

class EmailControllerTest extends TestCase
{
    private EmailController $controller;
    private $mockService;

    protected function setUp(): void
    {
        $this->mockService = $this->createMock(EmailValidationService::class);
        $this->controller = new EmailController();
        
        // Используем рефлексию для замены сервиса
        $reflection = new \ReflectionClass($this->controller);
        $property = $reflection->getProperty('emailValidationService');
        $property->setAccessible(true);
        $property->setValue($this->controller, $this->mockService);
    }

    public function testValidateEmails()
    {
        $this->mockService->method('validate')
            ->willReturn(new ValidationResult('test@example.com', true, true));

        $method = new \ReflectionMethod($this->controller, 'validateEmails');
        $method->setAccessible(true);

        $result = $method->invoke($this->controller, ['test@example.com']);
        
        $this->assertEquals([
            'test@example.com' => [
                'email' => 'test@example.com',
                'isValid' => true,
                'details' => [
                    'format' => true,
                    'dns' => true
                ]
            ]
        ], $result);
    }
}
