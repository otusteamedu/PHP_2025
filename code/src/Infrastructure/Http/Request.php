<?php

namespace Alisaselezneva\Code\Infrastructure\Http;

use Alisaselezneva\Code\Domain\Services\StringProcessor;

class Request
{
    private StringProcessor $processor;

    public function __construct()
    {
        $this->processor = new StringProcessor();
    }

    public function handle(): void
    {
        $input = $_POST['string'] ?? '';
        $this->processor->validate($input);
    }
}