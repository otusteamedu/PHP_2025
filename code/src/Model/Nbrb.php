<?php
namespace Ak14\Exchange\Model;
use Ak14\Exchange\Request\Nbrb as NbrbRequest;


class Nbrb
{
    /**
     * @var array Поля валюты, которые необходимо извлечь из ответа API.
     */
    public $currencyFields = array('Cur_Code', 'Cur_Name','Cur_Abbreviation');

    public $currencies;

    /**
     * Получает список валют из API НБРБ и фильтрует их по заданным полям.
     *
     * @return array Массив валют, где каждая валюта представлена массивом с выбранными полями.
     * @throws \JsonException
     */
    public function listOfCurrencies(): array
    {
        $result = array();
        $currencies = NbrbRequest::getCurrencies();

        if(!$currencies){
            return $result;
        }

        foreach ($currencies as $currency) {
            $result[] = array_intersect_key((array)$currency, array_flip($this->currencyFields));
        }

        return $result;
    }

    public function getRate($code=false, $params=[])
    {
//        $rates = NbrbRequest::getRates();
        $rates = NbrbRequest::getRates(431);
        return json_decode($rates, true, 512, 4194304);

    }


    /**
     * @throws \JsonException
     */
    public function currencyByCode(string $code): Nbrb
    {
        $response = $currencies = NbrbRequest::getCurrencies($code);
            var_dump($response);
//        return $result;
    }
}