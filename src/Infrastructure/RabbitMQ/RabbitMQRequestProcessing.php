<?php

declare (strict_types=1);

namespace App\Infrastructure\RabbitMQ;

use App\Application\UseCase\RabbitMQRequestProcessingInterface;
use App\Infrastructure\Repository\RequestRepository;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Output\OutputInterface;

final readonly class RabbitMQRequestProcessing implements RabbitMQRequestProcessingInterface
{
    public function __construct(
        private RabbitMQConsumer  $rabbitMQConsumer,
        private RabbitMQPublisher $rabbitMQPublisher,
        private RequestRepository $requestRepository
    )
    {
    }

    public function consume(OutputInterface $output): void
    {
        $self = $this;

        $this->rabbitMQConsumer->receive(
            fn($msg) => $self->callback($msg, $output)
        );
    }

    public function publish(): array
    {
        $data = [];
        $id = $this->requestRepository->insert();

        if ($id > 0) {
            $data['id'] = $id;
            $data['message'] = 'Request created';
            $data['type'] = 'success';

            $this->rabbitMQPublisher->send((string)$id);
        } else {
            $data['message'] = 'Request failed';
            $data['type'] = 'error';
        }

        return $data;
    }

    public function getStatus(int $id): array
    {
        $data = [
            'message' => 'Request failed',
            'type' => 'error',
        ];

        if ($id > 0) {
            $result = $this->requestRepository->find($id);

            if ($result !== false) {
                $data['message'] = 'Status: ' . \var_export($result['status'], true);
                $data['type'] = 'success';
                $data['status'] = $result['status'];
            }
        }

        return $data;
    }

    private function callback(AMQPMessage $msg, OutputInterface $output): void
    {
        $id = $msg->getBody();
        $this->requestRepository->update($id, true);
        $output->writeln('Request processed id: ' . $id);
    }
}
