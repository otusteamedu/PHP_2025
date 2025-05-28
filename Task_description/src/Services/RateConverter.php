<?php

declare(strict_types=1);

namespace App\Services;

use App\Exception\RateConverterException;
use App\RateRepositoryInterface;

class RateConverter
{
    public function __construct(private RateRepositoryInterface $rateRepository)
    {
    }

    public function getRate(int $amount, string $from, string $to): int
    {
        if ($from === 'CNY') {
            throw new RateConverterException("Unsupported currency");
        }

        if ($from === 'RUB') {
            throw new RateConverterException("Unsupported currency");
        }

        if ($from === 'USD') {
            throw new RateConverterException("Unsupported currency");
        }

//        $this->rateRepository->getRate($from, $to);
//        $this->rateRepository->getRate($from, $to);

        return $amount * $this->rateRepository->getRate($from, $to);
    }
}