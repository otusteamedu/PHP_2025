<?php

declare(strict_types=1);

namespace Tests\Api;

use JsonException;

class CheckEmailListApiTest extends AbstractApiTest
{
    private const SUCCESS_RESPONSE_MESSAGE = 'Email list is valid';
    private const FAILED_RESPONSE_MESSAGE = 'Email list is not valid';

    public function setUp(): void
    {
        error_reporting(E_ALL);
    }

    /**
     * @throws JsonException
     */
    public function testValidEmailListRequest(): void
    {
        $response = $this->sendPostRequest(
            [
                'emailList' => [
                    'test123@yandex.ru',
                    'test1234@yandex.ru',
                    'test2981eiuj@gmail.com',
                ],
            ],
        );

        $this->assertEquals(self::SUCCESS_RESPONSE_MESSAGE, $response['message']);
        $this->assertEmpty($response['data']);
    }

    /**
     * @throws JsonException
     */
    public function testEmptyEmailListRequest(): void
    {
        $response = $this->sendPostRequest(['emailList' => []]);

        $this->assertEquals(self::FAILED_RESPONSE_MESSAGE, $response['message']);
        $this->assertEmpty($response['data']['invalidEmailList']);
    }

    /**
     * @throws JsonException
     */
    public function testNotValidEmailListRequest(): void
    {
        $notValidEmailList = [
            'emailList' => [
                123,
                'test@test',
                'http://example.com',
                '',
            ],
        ];

        $response = $this->sendPostRequest($notValidEmailList);

        $this->assertEquals(self::FAILED_RESPONSE_MESSAGE, $response['message']);
        $this->assertEquals($notValidEmailList['emailList'], $response['data']['invalidEmailList']);
    }

    /**
     * @throws JsonException
     */
    public function testPartiallyNotValidEmailListRequest(): void
    {
        $validEmailList = [
            'test1234@yandex.ru',
            'test2981eiuj@gmail.com',
        ];
        $notValidEmailList = [
            123,
            'test@test',
            'http://example.com',
            '',
        ];

        $response = $this->sendPostRequest(['emailList' => array_merge($validEmailList, $notValidEmailList)]);

        $this->assertEquals(self::FAILED_RESPONSE_MESSAGE, $response['message']);
        $this->assertEquals($notValidEmailList, $response['data']['invalidEmailList']);
    }

    /**
     * @throws JsonException
     */
    public function testInvalidBodyRequest(): void
    {
        $response = $this->sendRequest(
            [
                CURLOPT_POSTFIELDS => 'some body',
                CURLOPT_RETURNTRANSFER => true,
            ]
        );

        $this->assertEquals(self::INVALID_BODY_MESSAGE, $response['message']);
        $this->assertEmpty($response['data']);
    }

    /**
     * @throws JsonException
     */
    public function testInvalidJsonBodyRequest(): void
    {
        $response = $this->sendPostRequest([]);

        $this->assertEquals(self::INVALID_BODY_MESSAGE, $response['message']);
        $this->assertEmpty($response['data']);
    }
}
