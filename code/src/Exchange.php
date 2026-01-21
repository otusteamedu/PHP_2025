<?php

namespace Ak14\Exchange;

use Ak14\Exchange\Model\Nbrb;

/**
 * Класс для работы с курсами валют.
 */
class Exchange
{
    protected $nbrb;
    public function __construct()
    {
        $this->nbrb = new Nbrb();
    }

    /**
     * Возвращает список всех валют
     * @return array
     * @throws \JsonException
     */
    public function currencies(): array
    {
        return $this->nbrb->listOfCurrencies();
    }

    public function rate(): array
    {
        return $this->nbrb->getRate();
    }

    public function currency(string $code): Nbrb
    {
        return $this->nbrb->currencyByCode($code);
    }

}