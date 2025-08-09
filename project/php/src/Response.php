<?php

class Response {
    private string $containerName;

    public function __construct() {
        $this->containerName = gethostname();
    }

    public function createResponse(int $statusCode, string $message): string {
        http_response_code($statusCode);
        header("X-Container-Name: {$this->containerName}");
        return $message . " Processed by: {$this->containerName}";
    }
}