<?php

namespace Pryaniki\App;

use Exception;

class App
{
    public function run(): string
    {
        if (isset($_REQUEST['test']) && $_REQUEST['test'] == 'Y') {
            ParenthesisValidator::runTests();
            die();
        }

        $actionValue = $_REQUEST['string'] ?? '';

        $parenthesisValidator = new ParenthesisValidator($actionValue);
        try {
            $parenthesisValidator->validate();
            return 'OK';
        } catch (Exception $e) {
            http_response_code(400);
            return $e->getMessage();
        }
    }
}