<?php

declare(strict_types=1);

namespace Tests\Api;

use JsonException;
use PHPUnit\Framework\TestCase;

class AbstractApiTest extends TestCase
{
    protected const HOST = 'http://host.docker.internal:82';
    protected const PATH = '';
    protected const SUCCESS_RESPONSE_CODE = 200;
    protected const INVALID_BODY_MESSAGE = 'Invalid request body';
    protected const INVALID_REQUEST_MESSAGE = 'Invalid request method';

    public function setUp(): void
    {
        error_reporting(E_ALL);
    }

    /**
     * @throws JsonException
     */
    public function testInvalidPathRequest(): void
    {
        $response = $this->sendGetRequest();

        $this->assertEquals(self::INVALID_REQUEST_MESSAGE, $response['message']);
        $this->assertEmpty($response['data']);
    }

    /**
     * @throws JsonException
     */
    public function sendPostRequest(array $requestData): array
    {
        return $this->sendRequest(
            [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($requestData, JSON_THROW_ON_ERROR),
                CURLOPT_RETURNTRANSFER => true,
            ],
        );
    }

    /**
     * @throws JsonException
     */
    public function sendGetRequest(): array
    {
        return $this->sendRequest(
            [
                CURLOPT_RETURNTRANSFER => true,
            ],
        );
    }

    /**
     * @throws JsonException
     */
    public function sendRequest(array $params): array
    {
        $curl = curl_init(self::HOST . static::PATH);
        curl_setopt_array(
            $curl,
            $params
        );
        $response = curl_exec($curl);

        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}
