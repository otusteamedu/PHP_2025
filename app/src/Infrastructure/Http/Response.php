<?php
declare(strict_types=1);

namespace App\Infrastructure\Http;

class Response implements \JsonSerializable
{
    private int $code {
        get {
            return $this->code;
        }
    }

    public function __construct(
        private string  $result {
            get {
                return $this->result;
            }
        },
        int             $code,
        private mixed   $data {
            get {
                return $this->data;
            }
        },
        private ?string $message = null {
            get {
                return $this->message;
            }
        },
    )
    {
        $this->setCode($code);
    }

    private function setCode(int $code): void
    {
        http_response_code($code);
        $this->code = $code;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public function isSuccess(): bool
    {
        return $this->code >= 200 && $this->code < 300;
    }

    public function asJson(): self
    {
        header('Content-Type: application/json; charset=utf-8');
        return $this;

    }
}