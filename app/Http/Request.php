<?php

namespace App\Http;

use App\Services\RouteService;
use Exception;

class Request
{
    const AVAILABLE_METHODS = ['GET', 'POST', 'PUT', 'DELETE'];

    /** @var string */
    protected string $method = 'GET';

    /** @var array|null */
    protected ?array $data = [];

    /** @var array|null */
    protected ?array $query = [];

    /** @var array|null */
    protected ?array $headers = [];

    /** @var RouteService */
    protected RouteService $routeService;

    /**
     * @param string $method
     * @param string $uri
     * @param array|null $data
     * @param array|null $headers
     * @throws Exception
     */
    public function __construct(
        string $method,
        string $uri,
        ?array $data,
        ?array $headers
    ) {
        $this->routeService = (new RouteService());

        $this->setQuery($_GET);
        $this->setRoute($uri, $method);
        $this->setMethod($method);
        $this->setData($data);
        $this->setHeaders($headers);
    }

    public function rules(): array {
        return [];
    }

    /**
     * @param string $uri
     * @param string $method
     * @return void
     * @throws Exception
     */
    private function setRoute(string $uri, string $method): void {
        $this->routeService->find($uri, $method);
    }

    /**
     * @param array $data
     * @return void
     */
    private function setData(array $data): void {
        $this->data = $data;
    }

    /**
     * @param array $query
     * @return void
     */
    private function setQuery(array $query): void {
        $this->query = $query;
    }

    /**
     * @param string $method
     * @return void
     * @throws Exception
     */
    private function setMethod(string $method): void {
        if (in_array($method, self::AVAILABLE_METHODS) === false) {
            throw new Exception('Method not allowed', 405);
        }

        $this->method = $method;
    }

    /**
     * @param array $headers
     * @return void
     */
    private function setHeaders(array $headers): void {
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    public function getRoute(): array {
        return $this->routeService->currentRoute;
    }

    /**
     * @return array
     */
    public function getData(): array {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getQuery(): array {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getMethod(): string {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getHeaders(): array {
        return $this->headers;
    }
}