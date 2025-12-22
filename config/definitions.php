<?php


use Dinargab\Homework20\Application\Job\Factory\JobFactory;
use Dinargab\Homework20\Application\Statement\Factory\BankStatementFactory;
use Dinargab\Homework20\Domain\Job\Factory\JobFactoryInterface;
use Dinargab\Homework20\Domain\Job\Repository\JobRepositoryInterface;
use Dinargab\Homework20\Domain\Logger\LoggerInterface;
use Dinargab\Homework20\Domain\Queue\QueueServiceInterface;
use Dinargab\Homework20\Domain\Statement\Factory\BankStatementFactoryInterface;
use Dinargab\Homework20\Domain\Statement\Repository\BankStatementRepositoryInterface;
use Dinargab\Homework20\Infrastructure\Logger\ConsoleLogger;
use Dinargab\Homework20\Infrastructure\Repository\JobRepository;
use Dinargab\Homework20\Infrastructure\Repository\StatementRepository;
use Dinargab\Homework20\Infrastructure\Services\QueueService;

return [
    JobRepositoryInterface::class => DI\autowire(JobRepository::class),
    JobFactoryInterface::class => DI\autowire(JobFactory::class),
    QueueServiceInterface::class => DI\autowire(QueueService::class),
    BankStatementFactoryInterface::class => DI\autowire(BankStatementFactory::class),
    BankStatementRepositoryInterface::class => DI\autowire(StatementRepository::class),
    LoggerInterface::class => DI\autowire(ConsoleLogger::class),
];