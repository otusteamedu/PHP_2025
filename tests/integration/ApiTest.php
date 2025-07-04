<?php

use PHPUnit\Framework\TestCase;

//проверяем взаимодействие между компонентами
class ApiTest extends TestCase
{
    private string $url;

    protected function setUp(): void
    {
        $this->url = 'http://mysite.local/api.php';
    }

    //позитивный тест, валидные email'ы возвращаются как массив
    public function testValidEmails()
    {
        $post = ['emails' => "test@gmail.com\ntest@example.com"];

        $response = $this->post($post);

        $this->assertIsArray($response);
        $this->assertContains('test@gmail.com', $response);
        $this->assertContains('test@example.com', $response);
    }

    //негативные тесты, невалидные email'ы → пустой массив
    public function testInvalidEmails()
    {
        $post = ['emails' => "invalid-email\nmissing-at-symbol.com"];

        $response = $this->post($post);

        $this->assertIsArray($response);
        $this->assertEmpty($response);
    }

    //если нет emails в POST → ошибка
    public function testMissingEmailsField()
    {
        $response = $this->post([]);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('error', $response);
        $this->assertStringContainsString('emails', $response['error']);
    }

    //если emails передан как массив → ошибка
    public function testEmailsIsNotString()
    {
        $post = ['emails' => ['array@not.allowed']];

        $response = $this->post($post);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('error', $response);
        $this->assertStringContainsString('emails', $response['error']);
    }


    //если пустая строка → ошибка
    public function testEmptyEmailsString()
    {
        $post = ['emails' => ''];

        $response = $this->post($post);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('error', $response);
        $this->assertStringContainsString("неверный формат: значение поля 'emails' пустое", mb_strtolower($response['error']));
    }

    private function post(array $postFields): array
    {
        $ch = curl_init($this->url);  // инициализация cURL
        curl_setopt_array($ch, [      // настройка параметров запроса
            CURLOPT_RETURNTRANSFER => true,     //вернуть ответ как строку
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postFields),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        $result = curl_exec($ch);     // выполняем HTTP-запрос
        curl_close($ch);              // завершаем сеанс cURL      

        $decoded = json_decode($result, true);

        return is_array($decoded) ? $decoded : ['error' => 'Invalid JSON'];
    }
}
