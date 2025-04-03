<?php

namespace App\Indexes;

use App\Services\ElasticService;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class Index
{
    /** @var ElasticService */
    protected ElasticService $elasticService;

    /** @var string */
    public string $name;

    public function __construct() {
        $this->elasticService = new ElasticService();
    }

    /**
     * @return array
     */
    public function settings(): array {
        return [];
    }

    /**
     * @param array|null $body
     * @return array
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function create(?array $body = []): array {
        if (empty($body)) {
            $body = $this->settings();
        }

        return $this->elasticService->create($this->name, $body);
    }

    /**
     * @param $id
     * @return array
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function get($id = null): array {
        return $this->elasticService->get($this->name, $id);
    }

    /**
     * @param string $json
     * @return array
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function bulk(string $json): array {
        return $this->elasticService->bulk($json);
    }

    /**
     * @param array|null $query
     * @return array
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function search(?array $query = []): array {
        return $this->elasticService->search($this->name, $query);
    }

    /**
     * @return bool
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function exists(): bool {
        return $this->elasticService->exists($this->name);
    }

    /**
     * @return bool
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function drop(): bool {
        return $this->elasticService->drop($this->name);
    }
}