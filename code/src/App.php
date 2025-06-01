<?php

namespace App;

require_once 'Validator.php';
require_once 'Response.php';

class App {
    public function run() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $string = $_POST['string'] ?? '';
            $validator = new Validator();
            $response = new Response();

            if (!$validator->isNotEmpty($string)) {
                return $response->badRequest("String cannot be empty.");
            }

            if ($validator->isValidParentheses($string)) {
                return $response->ok("String is valid.");
            } else {
                return $response->badRequest("String is invalid.");
            }
        }

        return "Invalid request method.";
    }
}
