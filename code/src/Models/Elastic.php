<?php

namespace Ak\Hw\Models;

use Exception;
use JsonException;

class Elastic
{
    public function __construct(
        protected string $index,
        public int $size = 100
    ) {
    }

    public function init($settings=[], $mappings=[]): void
    {
        $body = array_filter(compact('settings', 'mappings'));

        $request = [
            'method'       => 'PUT',
            'body'         => json_encode($body, JSON_THROW_ON_ERROR),
            'endpoint'     => '',
            'content_type' => 'application/json',
        ];

        $this->request($request);
    }

    /**
     * @throws Exception
     */
    public function bulk(string $filePath): void
    {
        if (!file_exists($filePath)) {
            die("File not found: $filePath");
        }

        $request = [
            'method'       => 'POST',
            'body'         => file_get_contents($filePath),
            'endpoint'     => '_bulk',
            'content_type' => 'application/x-ndjson',
        ];

        $this->request($request);
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function search(array $query): mixed
    {
        $query['size'] = $this->size;

        $request = [
            'method'       => 'POST', // Use POST for search queries with a body
            'body'         => json_encode($query, JSON_THROW_ON_ERROR),
            'endpoint'     => '_search', // Use the _search endpoint
        ];
        $response = $this->request($request);
        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws Exception
     */
    private function request(array $data): string
    {
        $curl = curl_init();

        $url = 'http://elasticsearch:9200/' . $this->index;

        if (!empty($data['endpoint'])) {
            $url .= '/' . $data['endpoint'];
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $data['method']);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: ' . ($data['content_type'] ?? 'application/json'),
        ]);

        if (!empty($data['body'])) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data['body']);
        }

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl); // Close curl before throwing
            throw new Exception($error);
        }

        curl_close($curl);

        if ($response === false) {
            throw new Exception('cURL request failed without a specific error.');
        }

        return $response;
    }
}
