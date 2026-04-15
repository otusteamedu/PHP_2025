<?php

declare(strict_types=1);

namespace Otus\Queue\Application\Amqp {
    function flush(): void
    {
        $GLOBALS['otus_amqp_flush_called'] = true;
    }

    function ob_flush(): bool
    {
        $GLOBALS['otus_amqp_ob_flush_called'] = true;

        return true;
    }
}

namespace Otus\Queue\Tests\Application\Amqp {
    use Otus\Queue\Application\Amqp\Chat;
    use PhpAmqpLib\Message\AMQPMessage;
    use PHPUnit\Framework\TestCase;

    final class ChatTest extends TestCase
    {
        protected function setUp(): void
        {
            $GLOBALS['otus_amqp_flush_called'] = false;
            $GLOBALS['otus_amqp_ob_flush_called'] = false;
        }

        public function testInvokeAcknowledgesMessageAndPrintsSseData(): void
        {
            $message = $this->createMock(AMQPMessage::class);
            $message->expects(self::once())->method('ack');
            $message->expects(self::once())->method('getBody')->willReturn('{"event":"ok"}');

            $chat = new Chat();

            ob_start();
            $chat($message);
            $output = ob_get_clean();

            self::assertStringContainsString('data: {"event":"ok"}', (string) $output);
            self::assertTrue($GLOBALS['otus_amqp_flush_called']);
        }
    }
}
