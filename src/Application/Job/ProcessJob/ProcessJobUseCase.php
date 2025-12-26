<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Application\Job\ProcessJob;

use Dinargab\Homework20\Domain\Job\Entity\Job;
use Dinargab\Homework20\Domain\Job\JobStatusEnum;
use Dinargab\Homework20\Domain\Job\Repository\JobRepositoryInterface;
use Dinargab\Homework20\Domain\Logger\LoggerInterface;
use Dinargab\Homework20\Domain\Queue\QueueServiceInterface;
use Dinargab\Homework20\Domain\Statement\Factory\BankStatementFactoryInterface;
use Dinargab\Homework20\Domain\Statement\Repository\BankStatementRepositoryInterface;

class ProcessJobUseCase
{

    public function __construct(
        private QueueServiceInterface $queueService,
        private JobRepositoryInterface $jobRepository,
        private BankStatementFactoryInterface $bankStatementFactory,
        private BankStatementRepositoryInterface $bankStatementRepository,
        private LoggerInterface $logger
    )
    {

    }



    public function __invoke()
    {
        $callback = function (Job $job) {
            yield $job;
        };
        $generator = $this->queueService->pull($callback);

        /** @var Job $generatorJob */
        foreach ($generator as $generatorJob) {
            if (!$generatorJob) {
                continue;
            }
            $statement = $this->bankStatementFactory->createFromJob($generatorJob, "https://example.com");
            $statement = $this->bankStatementRepository->addStatement($statement);
            $generatorJob->setBankStatementId($statement->getId());
            $generatorJob->setStatus(JobStatusEnum::COMPLETED);
            $this->jobRepository->update($generatorJob);
            $this->logger->log($generatorJob);
        }
    }
}