<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Application\UseCase;

use MkdBot\Application\UseCase\ProcessRagQuery;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ProcessRagQueryTest extends TestCase
{
    public function testExecuteLogsInfo(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info')->with($this->stringContains('заглушка'));

        $useCase = new ProcessRagQuery($logger);
        $useCase->execute(12345, 'Как оплатить ЖКХ?');
    }
}
