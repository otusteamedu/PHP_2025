<?php

namespace Src;

class ExceptionHandler {
    public static function handle(\Exception $e, int $code = 400): string {
        http_response_code($code);
        return json_encode(["error" => $e->getMessage()]);
    }
}
