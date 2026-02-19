<?php
declare(strict_types=1);

namespace Ak\Hw\Test\System;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class OrderSystemTest extends TestCase
{
    private Client $client;
    private string $baseUrl;

    protected function setUp(): void
    {
        // Используем специальное имя хоста для доступа к хост-машине из Docker
        $this->baseUrl = 'http://host.docker.internal:8000'; 

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'http_errors' => false, // Не выбрасывать исключения на 4xx/5xx коды
        ]);
    }

    public function testGetRequestShowsForm(): void
    {
        $response = $this->client->request('GET', '/index.php');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('<form id="payment-form"', (string)$response->getBody());
        $this->assertStringContainsString('Payment Form', (string)$response->getBody());
    }

    public function testPostWithValidDataShowsSuccess(): void
    {
        $validData = [
            'form_params' => [
                "card_number" => "4111 1111 1111 1111",
                "card_holder" => "John Doe",
                "card_expiration" => "12/28",
                "cvv" => "602",
                "sum" => 10.1,
                "order_number" => "1"
            ]
        ];

        $response = $this->client->request('POST', '/index.php', $validData);
        $html = (string)$response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Payment was successful!', $html);
    }

    public function testPostWithInvalidDataShowsErrors(): void
    {
        $invalidData = [
            'form_params' => [
                "card_number" => "invalid-number",
                "card_holder" => "John Doe 123",
                "card_expiration" => "01/20", // Просроченная карта
                "cvv" => "1",
                "sum" => -10,
                "order_number" => ""
            ]
        ];

        $response = $this->client->request('POST', '/index.php', $invalidData);
        $html = (string)$response->getBody();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertStringContainsString('Invalid card number format', $html);
        $this->assertStringContainsString('Invalid characters in card holder name', $html);
        $this->assertStringContainsString('Card has expired', $html);
        $this->assertStringContainsString('Invalid CVV format', $html);
        $this->assertStringContainsString('Sum must be a positive number', $html);
        $this->assertStringContainsString('Order number is required', $html);
    }
}
