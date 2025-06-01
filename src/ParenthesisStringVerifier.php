<?php

declare(strict_types=1);

namespace App;

class ParenthesisStringVerifier
{
    private ?int $httpCode = null;
    private ?string $message = null;

    public function run(): self
    {
        try {
            $this->verify();
            $this->httpCode = 200;
            $this->message = 'Всё хорошо';
        } catch (\Exception $e) {
            $this->httpCode = $e->getCode();
            $this->message = $e->getMessage();
        } finally {
            return $this;
        }
    }

    public function getHttpCode(): ?int
    {
        return $this->httpCode;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @throws \Exception
     */
    private function verify(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new \Exception('Method Not Allowed (Allow: POST)', 405);
        }

        $body = file_get_contents('php://input');
        if ($body === false) {
            throw new \Exception('Не удалось получить данные из тела запроса', 400);
        }
        try {
            $body = json_decode($body, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \Exception($e->getMessage(), 400);
        }

        if (!isset($body['string'])) {
            throw new \Exception('Параметр "string" не передан в теле запроса', 400);
        }

        if (!ParenthesisStringValidator::isValid($body['string'])) {
            throw new \Exception('Всё плохо', 400);
        }
    }
}
