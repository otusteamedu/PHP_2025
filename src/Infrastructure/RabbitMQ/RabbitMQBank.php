<?php

declare (strict_types=1);

namespace App\Infrastructure\RabbitMQ;

use App\Application\UseCase\RabbitMQBankInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Output\OutputInterface;

class RabbitMQBank implements RabbitMQBankInterface
{
    private RabbitMQConsumer $rabbitMQConsumer;

    private RabbitMQPublisher $rabbitMQPublisher;

    public function __construct(
        RabbitMQConsumer $rabbitMQConsumer,
        RabbitMQPublisher $rabbitMQPublisher
    )
    {
        $this->rabbitMQConsumer = $rabbitMQConsumer;
        $this->rabbitMQPublisher = $rabbitMQPublisher;
    }

    public function consume(OutputInterface $consoleOutput): void
    {
        $self = $this;

        $this->rabbitMQConsumer->receive(
            fn($msg) => $self->callback($msg, $consoleOutput)
        );
    }

    public function publish(): void
    {
        $this->rabbitMQPublisher->send('!!!!!===== Message bank =====!!!!!');
    }

    private function callback(AMQPMessage $msg, OutputInterface $consoleOutput): void
    {
        $consoleOutput->writeln($msg->getBody());
    }
}
