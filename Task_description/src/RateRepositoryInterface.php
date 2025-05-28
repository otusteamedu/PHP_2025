<?php

declare(strict_types=1);

namespace App;

interface RateRepositoryInterface
{
    public function getRate(string $from, string $to): int;
}