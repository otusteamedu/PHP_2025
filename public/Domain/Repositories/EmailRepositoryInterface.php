<?php

namespace Crowley\Hw\Domain\Repositories;

interface EmailRepositoryInterface
{

    public function checkMxRecords(array $emails): array;

}