<?php

namespace Tests;

use App\Application;
use App\Controller\EmailVerificationController;
use App\Http\Request;
use App\Http\Response;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRunDelegatesToController(): void
    {
        $controller = Mockery::mock(EmailVerificationController::class);
        $expected = Response::json(['ok' => true]);

        $controller->shouldReceive('handle')
            ->once()
            ->with(Mockery::type(Request::class))
            ->andReturn($expected);

        $result = (new Application($controller))->run();

        $this->assertSame($expected, $result);
    }

    public function testCreateBuildsApplication(): void
    {
        $this->assertInstanceOf(Application::class, Application::create());
    }
}
