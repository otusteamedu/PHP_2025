<?php

namespace Crowley\Hw\Application\Services;

use Crowley\Hw\Domain\Repositories\EmailRepositoryInterface;

class EmailService
{

    private EmailRepositoryInterface $emailRepository;
    public function __construct(EmailRepositoryInterface $emailRepository) {
        $this->emailRepository = $emailRepository;
    }

    public function getMxRecords(array $emails): array {
        return $this->emailRepository->checkMxRecords($emails);
    }

}