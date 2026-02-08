<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\Controllers\Actions;

use App\Application\Interfaces\ValidateEmailsUseCaseInterface;
use App\Domain\DTO\EmailValidationResult;
use App\Infrastructure\Http\Request;
use App\Presentation\Controllers\Actions\ValidateEmailsAction;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ValidateEmailsActionTest extends TestCase
{
    private ValidateEmailsUseCaseInterface&MockObject $useCase;
    private ValidateEmailsAction $action;

    protected function setUp(): void
    {
        $this->useCase = $this->createMock(ValidateEmailsUseCaseInterface::class);
        $this->action = new ValidateEmailsAction($this->useCase);
    }

    public function testSupportsReturnsTrue(): void
    {
        $request = new Request([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/emails',
        ]);

        $this->assertTrue($this->action->supports($request));
    }

    public function testSupportsReturnsFalse(): void
    {
        $request = new Request([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/emails',
        ]);

        $this->assertFalse($this->action->supports($request));
    }

    public function testSupportsReturnsFalseToOtherPath(): void
    {
        $request = new Request([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/other',
        ]);

        $this->assertFalse($this->action->supports($request));
    }

    public function testHandleReturnsErrorWhenBodyIsEmpty(): void
    {
        $request = $this->createMock(Request::class);
        $request->method('getBody')->willReturn([]);

        $response = $this->action->handle($request);

        $reflection = new \ReflectionClass($response);
        $contentProperty = $reflection->getProperty('content');
        $contentProperty->setAccessible(true);
        $statusCodeProperty = $reflection->getProperty('statusCode');
        $statusCodeProperty->setAccessible(true);

        $content = json_decode($contentProperty->getValue($response), true);

        $this->assertFalse($content['success']);
        $this->assertStringContainsString('email', $content['error']);
        $this->assertEquals(400, $statusCodeProperty->getValue($response));
    }

    public function testHandleReturnsSuccess(): void
    {
        $emails = ['test@example.com', 'user@domain.org'];

        $request = $this->createMock(Request::class);
        $request->method('getBody')->willReturn($emails);

        $results = [
            new EmailValidationResult('test@example.com', true),
            new EmailValidationResult('user@domain.org', true),
        ];

        $this->useCase->method('execute')->willReturn($results);

        $response = $this->action->handle($request);

        $reflection = new \ReflectionClass($response);
        $contentProperty = $reflection->getProperty('content');
        $contentProperty->setAccessible(true);
        $statusCodeProperty = $reflection->getProperty('statusCode');
        $statusCodeProperty->setAccessible(true);

        $content = json_decode($contentProperty->getValue($response), true);

        $this->assertTrue($content['success']);
        $this->assertCount(2, $content['data']);
        $this->assertEquals(200, $statusCodeProperty->getValue($response));
    }

    public function testHandleReturnsValidationResults(): void
    {
        $emails = ['valid@test.com', 'invalid'];

        $request = $this->createMock(Request::class);
        $request->method('getBody')->willReturn($emails);

        $results = [
            new EmailValidationResult('valid@test.com', true),
            new EmailValidationResult('invalid', false, 'Invalid format'),
        ];

        $this->useCase->method('execute')->willReturn($results);

        $response = $this->action->handle($request);

        $reflection = new \ReflectionClass($response);
        $contentProperty = $reflection->getProperty('content');
        $contentProperty->setAccessible(true);

        $content = json_decode($contentProperty->getValue($response), true);

        $this->assertTrue($content['success']);
        $this->assertEquals('valid@test.com', $content['data'][0]['email']);
        $this->assertTrue($content['data'][0]['is_valid']);
        $this->assertEquals('invalid', $content['data'][1]['email']);
        $this->assertFalse($content['data'][1]['is_valid']);
        $this->assertEquals('Invalid format', $content['data'][1]['error']);
    }
}
