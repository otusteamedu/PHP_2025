<?php

declare(strict_types=1);

namespace App;

class DummyRateRepository implements RateRepositoryInterface
{
    public function getRate(string $from, string $to): int
    {
        return 10;
    }

    public function getRate2(string $from, string $to): int
    {
        return 20;
    }
}