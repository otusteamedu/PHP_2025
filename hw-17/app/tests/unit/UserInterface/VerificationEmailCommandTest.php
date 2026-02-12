<?php

declare(strict_types=1);

namespace unit\UserInterface;

use App\Application\VerificationEmailService;
use App\UserInterface\VerificationEmailCommand;
use Codeception\Test\Unit;

final class VerificationEmailCommandTest extends Unit
{
    public function testHandleCallsServiceHandle(): void
    {
        $serviceMock = $this->createMock(VerificationEmailService::class);

        $serviceMock
            ->expects($this->once())
            ->method('handle');

        $command = new VerificationEmailCommand($serviceMock);

        $command->handle();
    }
}
