<?php

declare(strict_types=1);

namespace App\Controller\Cli\Command;

use App\Controller\Amqp\BankReport\BankReportConsumer;
use App\Core\Container\Config\Contracts\DotEnvConfigInterface;
use App\Domain\BankReport\ReportService;
use App\Infrastructure\RabbitMq\Connection\AmqpConnectionInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand('rabbitmq:consume:bank_reports')]
class ConsumeBankReportsCommand extends Command
{
    public function __construct(
        private readonly AmqpConnectionInterface $connection,
        private readonly DotEnvConfigInterface $config,
        private readonly ReportService $reportService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                name: 'max-attempts',
                mode: InputOption::VALUE_REQUIRED,
                description: 'Max retry attempts',
                default: $this->config->get('RABBITMQ_BANK_REPORTS_MAX_ATTEMPTS', '3'),
            )
            ->addOption(
                name: 'prefetch-count',
                mode: InputOption::VALUE_REQUIRED,
                description: 'QoS prefetch count',
                default: $this->config->get('RABBITMQ_BANK_REPORTS_PREFETCH_COUNT', '1'),
            )
        ;
    }

    public function __invoke(InputInterface $input): int
    {
        $queueName = $this->config->get('RABBITMQ_BANK_REPORTS_QUEUE', 'bank_reports');
        $maxAttempts = (int) $input->getOption('max-attempts');
        $prefetchCount = (int) $input->getOption('prefetch-count');

        try {
            $consumer = new BankReportConsumer(
                reportService: $this->reportService,
                connection: $this->connection,
                queueName: $queueName,
                maxAttempts: $maxAttempts,
                prefetchCount: $prefetchCount,
            );

            $consumer->consume();

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            fwrite(STDERR, ' [ERROR] ' . $e->getMessage() . "\n");
            return Command::FAILURE;
        }
    }
}
