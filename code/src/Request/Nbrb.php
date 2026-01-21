<?php

namespace Ak14\Exchange\Request;

use JetBrains\PhpStorm\NoReturn;
use JsonException;

/**
 * Класс Nbrb
 *
 * Этот класс предназначен для взаимодействия с API
 * для получения информации о курсах валют.
 */
class Nbrb
{
    private static $apiUrl = 'https://api.nbrb.by/exrates/';

    /**
     * @throws JsonException
     */
    public static function getCurrencies($code=''): array
    {
        $response = self::requestApi('currencies/' . $code);

        if (!$response) {
            return [];
        }

        $result = json_decode($response, false, 512, 4194304);
        if (is_array($result)) {
            return $result;
        }

        if (is_object($result)) {
            return [$result];
        }

        return [];
    }


    public static function getRates($code = false, $params=[])
    {
        $paramsStr = '';
        if(!$params['periodicity']){
            $paramsStr = '?periodicity=1';
        }

        $response = self::requestApi('rates/'.$code . $paramsStr );
        return $response;
//        var_dump($paramsStr, $response);
//        die;
    }

    /**
     * @throws JsonException
     */
    private static function requestApi(string $url)
    {
        $curl = curl_init();

        var_dump(self::$apiUrl.$url);

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::$apiUrl.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            throw new \RuntimeException("cURL error: " . $error_msg);
        }
        curl_close($curl);

        return $response;
    }
}
