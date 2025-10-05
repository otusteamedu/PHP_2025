<?php

namespace Blarkinov\ElasticApp\Service\ElasticSearch;

use Blarkinov\ElasticApp\CLI\Response;
use Blarkinov\ElasticApp\Http\Request;
use Exception;
use stdClass;

class ElasticSearch
{
    public const PARAM_FILTER_CATEGORY = "category";
    public const PARAM_FILTER_MIN_PRICE = "min-price";
    public const PARAM_FILTER_MAX_PRICE = "max-price";
    public const PARAM_FILTER_QUANTITY = "quantity";
    public const PARAM_FILTER_ENABLED = "enabled";

    public const PARAM_SEARCH_TERM = "term";

    private static string $dbName;
    private static string $storagePath = __DIR__ . '/../../Storage/books.json';

    private Request $request;
    private Response $response;
    private string $url;


    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();

        $this->url = $_ENV["URL_ELASTIC"];
    }

    public static function getDbName(): string
    {

        if (!isset(self::$dbName)) {
            self::$dbName = $_ENV["DATA_BASE_NAME"];
        }

        return self::$dbName;
    }

    public function createDB(): bool
    {
        $headers = [
            'Content-Type: application/json',
        ];

        $configDB = file_get_contents(__DIR__ . '/../../config.json');

        $options = [
            CURLOPT_URL => $this->url . '/' . self::getDbName(),
            CURLOPT_CUSTOMREQUEST  => 'PUT',
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $configDB,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];

        $response = $this->request->send($options);

        if ($response['httpcode'] === 200)
            return true;
        else {
            $response = json_decode($response['response']);

            if (isset($response->error->type))
                if ($response->error->type === 'resource_already_exists_exception')
                    return true;

            return false;
        }
    }

    public function fillFull(): bool
    {

        if ($this->isFullDB())
            return true;

        $data = file_get_contents(self::$storagePath);

        $headers = [
            'Content-Type: application/json',
        ];

        $options = [
            CURLOPT_URL => $this->url . '/_bulk',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];

        $response = $this->request->send($options);

        if ($response['httpcode'] === 200)
            return true;

        return false;
    }

    private function isFullDB(): bool
    {

        $headers = [
            'Content-Type: application/json',
        ];

        $data = json_encode(['size' => 0]);

        $options = [
            CURLOPT_URL => $this->url . '/' . self::getDbName() . '/_search',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];

        $response = $this->request->send($options);


        if ($response['httpcode'] === 200) {
            $response = json_decode($response['response']);
            if (isset($response->hits->total->value))
                if ($response->hits->total->value !== 0)
                    return true;
        }



        return false;
    }


    public function search(array $args)
    {

        $params = $this->getParams($args);
        $filter = (new Filter)->get($params);

        $headers = [
            'Content-Type: application/json',
        ];

        $data = [
            "size" => 1000,
            "query" => [
                "bool" => [
                    "must" => [
                        [
                            "match" => [
                                "title" => [
                                    "query" => $params['title'],
                                    "fuzziness" => "auto",
                                ]
                            ]
                        ]
                    ],
                ]
            ]
        ];

        if (!empty($filter['filter']))
            $data['query']['bool']['filter'] = $filter['filter'];

        if (!empty($filter['must']))
            $data['query']['bool']['must'][] = $filter['must'];

        var_dump(json_encode($data));


        $options = [
            CURLOPT_URL =>  $this->url . '/' . self::getDbName() . "/_search",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];

        $response = $this->request->send($options);

        var_dump($response['response']);

        if ($response['httpcode'] === 200) {
            $response = json_decode($response['response'], true);
            if (isset($response['hits']['total']['value']))
                if ($response['hits']['total']['value'] !== 0)
                    $this->response->send(0, "", $response['hits']['hits']);
                else
                    $this->response->send(0, "not found search string");
        }

        new Exception('failed request Elastic Search');
    }

    public function delete(): bool
    {
        $headers = [
            'Content-Type: application/json',
        ];


        $options = [
            CURLOPT_URL => $this->url . '/' . self::getDbName(),
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];

        $response = $this->request->send($options);

        if ($response['httpcode'] === 200)
            return true;
        else
            return false;
    }


    public function createDocument(string $name, ?string $id = null): bool
    {
        $headers = [
            'Content-Type: application/json',
        ];

        $url = $this->url . '/' . self::getDbName() . "/$name";

        $options = [
            CURLOPT_URL => $id ? $url . "/$id" : $url,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
        ];

        $response = $this->request->send($options);


        if ($response['httpcode'] === 200)
            return true;
        else {
            $response = json_decode($response['response']);

            if (isset($response->error->type))
                if ($response->error->type === 'resource_already_exists_exception')
                    return true;

            return false;
        }
    }

    private function getParams(array $args): array
    {
        $params = [
            'title' => $args[1],
            self::PARAM_FILTER_CATEGORY => '',
            self::PARAM_FILTER_MIN_PRICE => -1,
            self::PARAM_FILTER_MAX_PRICE => -1,
            self::PARAM_FILTER_ENABLED => false,
            self::PARAM_FILTER_QUANTITY => -1,
        ];

        $args = array_slice($args, 2);

        foreach ($args as $value) {

            $value = explode("=", $value);

            if ($value[0] !== '--'.self::PARAM_FILTER_ENABLED)
                $params[substr($value[0],2)] = $value[1];
            else
                $params[substr($value[0],2)] = true;
        }

        return $params;
    }
}
