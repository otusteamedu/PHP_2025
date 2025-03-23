<?php

namespace app\Elasticsearch;

class ElasticsearchClient {
    private string $url;
    private array $headers;

    public function __construct($url, $username, $password) {
        $this->url = $url;
        $auth = base64_encode("$username:$password");
        $this->headers = [
            'Authorization: Basic ' . $auth,
            'Content-Type: application/json'
        ];
    }

    public function request($method, $endpoint, $data = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . $endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            die('Ошибка cURL: ' . curl_error($ch));
        }
        curl_close($ch);
        return $response;
    }
}