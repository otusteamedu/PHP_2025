<?php

namespace Tests\Controller;

use App\Controller\EmailVerificationController;
use App\Http\Request;
use App\Service\EmailVerificationService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use phpmock\mockery\PHPMockery;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class EmailVerificationControllerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testHandleReturnsHandlePost()
    {
        PHPMockery::mock('App\Http', 'http_response_code')
            ->once()
            ->with(200)
            ->andReturn(200);

        PHPMockery::mock('App\Http', 'header')
            ->once()
            ->with('Content-Type: application/json')
            ->andReturnNull();

        $service = Mockery::mock(EmailVerificationService::class);
        $service->shouldReceive('verifyMultiple')->once()->with([])->andReturn([]);

        $controller = new EmailVerificationController($service);

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('isPost')->once()->andReturn(true);
        $request->shouldReceive('getEmailsInput')->once()->andReturn('');

        $result = $controller->handle($request)->send();

        $this->assertJson($result);
    }

    public function testHandleReturnsHandleGet()
    {
        PHPMockery::mock('App\Http', 'http_response_code')
            ->once()
            ->with(200)
            ->andReturn(200);

        PHPMockery::mock('App\Http', 'header')
            ->once()
            ->with('Content-Type: text/html')
            ->andReturnNull();

        $service = Mockery::mock(EmailVerificationService::class);
        $service->shouldNotReceive('verifyMultiple');

        $controller = new EmailVerificationController($service);

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('isPost')->once()->andReturn(false);

        $result = $controller->handle($request)->send();

        $this->assertStringContainsString('<html', $result);
    }

    public function testParseEmailsTrimsSplitsAndFiltersEmptyLines(): void
    {
        $service = Mockery::mock(EmailVerificationService::class);
        $controller = new EmailVerificationController($service);

        $method = (new ReflectionClass($controller))->getMethod('parseEmails');

        $input = "  a@example.com  " . PHP_EOL . PHP_EOL . "  b@test.com  ";
        $result = $method->invoke($controller, $input);

        $this->assertSame([0 => 'a@example.com', 2 => 'b@test.com',], $result);
    }

    public function testParseEmailsReturnsEmptyArrayForBlankInput(): void
    {
        $service = Mockery::mock(EmailVerificationService::class);
        $controller = new EmailVerificationController($service);

        $method = (new ReflectionClass($controller))->getMethod('parseEmails');

        $result = $method->invoke($controller, " \n \n  ");

        $this->assertSame([], $result);
    }

    public function testCreateBuildsController(): void
    {
        $this->assertInstanceOf(EmailVerificationController::class, EmailVerificationController::create());
    }
}
