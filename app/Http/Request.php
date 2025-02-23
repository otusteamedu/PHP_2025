<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Http;

class Request
{

    private const METHOD_POST = 'POST';

    public function isPostMethod(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === self::METHOD_POST;
    }

    public function getPostValueByKey(string $key): string
    {
        return isset($_POST[$key]) ? $_POST[$key] : '';
    }
}
