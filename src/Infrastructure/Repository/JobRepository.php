<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Infrastructure\Repository;

use Dinargab\Homework20\Domain\Job\Entity\Job;
use Dinargab\Homework20\Domain\Job\Factory\JobFactoryInterface;
use Dinargab\Homework20\Domain\Job\JobStatusEnum;
use Dinargab\Homework20\Domain\Job\Repository\JobRepositoryInterface;
use Dinargab\Homework20\Domain\ValueObject\JobParameters;
use Dinargab\Homework20\Infrastructure\Client\PostgreSQLClient;
use PDO;
use ReflectionClass;

class JobRepository implements JobRepositoryInterface
{

    public function __construct(
        private PostgreSQLClient    $client,
        private JobFactoryInterface $jobFactory,
    )
    {

    }

    public function addJob(Job $job): Job
    {
        $stmt = $this->client->getConnection()->prepare("INSERT INTO job (parameters, status) VALUES (:parameters, :status)");
        $stmt->bindValue(':parameters', json_encode($job->getJobParameters()), PDO::PARAM_STR);
        $stmt->bindValue(':status', JobStatusEnum::NEW->value);
        $stmt->execute();
        $jobId = $this->client->getConnection()->lastInsertId();
        $reflection = new ReflectionClass($job);
        $reflectionProperty = $reflection->getProperty("id");
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($job, $jobId);
        $reflectionProperty->setAccessible(false);
        return $job;
    }

    public function getJobs(): array
    {
        $result = $this->client->getConnection()->query("SELECT * FROM job")->fetchAll(PDO::FETCH_ASSOC);
        $jobs = [];
        foreach ($result as $row) {
            $job = $this->createJobFromRow($row);
            $jobs[] = $job;
        }
        return $jobs;
    }

    public function getJobById(int $jobId): ?Job
    {
        $sth = $this->client->getConnection()->prepare("SELECT * FROM job WHERE id = :id");
        $sth->bindValue(":id", $jobId, PDO::PARAM_INT);
        $sth->execute();

        if ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            return $this->createJobFromRow($row);
        }
        return null;
    }

    public function update(Job $job): void
    {
        $sth = $this->client->getConnection()->prepare("UPDATE job SET parameters = :parameters, status = :status, statement = :statement WHERE id = :id");
        $sth->bindValue(":parameters", json_encode($job->getJobParameters()), PDO::PARAM_STR);
        $sth->bindValue(":status", $job->getStatus()->value);
        $sth->bindValue(":statement", $job->getBankStatementId());
        $sth->bindValue(":id", $job->getId(), PDO::PARAM_INT);
        $sth->execute();
    }

    public function setStatus(Job $job, JobStatusEnum $statusEnum): Job
    {
        $sth = $this->client->getConnection()->prepare("UPDATE job SET status = :status WHERE id = :id");
        $sth->bindValue(":id", $job->getId(), PDO::PARAM_INT);
        $sth->bindValue(":status", $statusEnum->value, PDO::PARAM_STR);
        $sth->execute();
        return $job->setStatus($statusEnum);
    }


    private function createJobFromRow(array $row): Job
    {
        $parametersData = json_decode($row['parameters'], true, 512, JSON_THROW_ON_ERROR);

        $jobParameters = JobParameters::fromArray($parametersData);

        $job = $this->jobFactory->createFromParameters($jobParameters);
        $job->setStatus(JobStatusEnum::from($row['status']));
        $job->setBankStatementId(is_null($row['statement']) ? $row["statement"] : (int) $row['statement']);
        $reflection = new ReflectionClass($job);

        $idProperty = $reflection->getProperty("id");
        $idProperty->setAccessible(true);
        $idProperty->setValue($job, (int)$row['id']);
        $idProperty->setAccessible(false);

        return $job;

    }

}