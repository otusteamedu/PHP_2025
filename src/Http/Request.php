<?php

namespace Blarkinov\ElasticApp\Http;

use Throwable;

class Request
{

    public function __construct() {}

    /**
     * @return array ['response'=>string JSON,'httpcode'=>int]
     */
    public function send(array $options): array
    {
        try {

            $ch = curl_init();

            curl_setopt_array($ch, $options);

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            return ['response' => $response, 'httpcode' => $httpcode];
        } catch (\Throwable $th) {
            return ['response' => false, 'httpcode' => '0'];
        }
    }
}
