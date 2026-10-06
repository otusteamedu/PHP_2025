<?php

declare(strict_types=1);

namespace App\Controller\Cli\Command;

use App\Controller\Amqp\BankReport\BankReportConsumer;
use App\Core\Container\Config\Contracts\DotEnvConfigInterface;
use App\Domain\BankReport\ReportService;
use App\Infrastructure\Mail\Factory\ReportMailerFactory;
use App\Infrastructure\Mail\Simulation\ReportMailerSimulation;
use App\Infrastructure\RabbitMq\Connection\AmqpConnectionInterface;
use App\Infrastructure\RabbitMq\Exception\AmqpConnectionException;
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
        private readonly ReportMailerFactory $mailerFactory,
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
            ->addOption(
                name: 'simulation',
                mode: InputOption::VALUE_REQUIRED,
                description: 'SMTP simulation mode (ok, down, flaky)',
                default: $this->config->get('BANK_REPORTS_MAILER_SIMULATION', 'ok'),
            )
        ;
    }

    public function __invoke(InputInterface $input): int
    {
        $queueName = $this->config->get('RABBITMQ_BANK_REPORTS_QUEUE', 'bank_reports');
        $prefetchCount = (int) $input->getOption('prefetch-count');

        try {
            $maxAttempts = (int) $input->getOption('max-attempts');
            if ($maxAttempts < 1) {
                throw new \InvalidArgumentException('max-attempts option must be positive integer');
            }

            $simulation = ReportMailerSimulation::tryFrom($input->getOption('simulation'));
            if ($simulation === null) {
                throw new \InvalidArgumentException(
                    sprintf('Unknown simulation mode: "%s"', $input->getOption('simulation')),
                );
            }

            $reportMailer = $this->mailerFactory->create($simulation, $maxAttempts);

            $consumer = new BankReportConsumer(
                reportService: $this->reportService,
                reportMailer: $reportMailer,
                connection: $this->connection,
                queueName: $queueName,
                maxAttempts: $maxAttempts,
                prefetchCount: $prefetchCount,
            );
            $consumer->consume();

            return Command::SUCCESS;

        } catch (AmqpConnectionException $e) {
            fwrite(STDERR, ' [CONNECTION] ' . $e->getMessage() . "\n");
            return Command::FAILURE;

        } catch (\InvalidArgumentException $e) {
            fwrite(STDERR, ' [INVALID] ' . $e->getMessage() . "\n");
            return  Command::INVALID;

        } catch (\Throwable $e) {
            fwrite(STDERR, ' [ERROR] ' . $e->getMessage() . "\n");
            $this->printPreviousChain($e);
            return Command::FAILURE;
        }
    }

    private function printPreviousChain(\Throwable $e): void
    {
        $previous = $e->getPrevious();
        while ($previous !== null) {
            fwrite(STDERR, ' [DETAILS] ' . $previous->getMessage() . "\n");
            $previous = $previous->getPrevious();
        }
    }
}
