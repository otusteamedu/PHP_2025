<?php

namespace Pryaniki\App;

use Exception;

class App
{
    public function run(): string
    {
        try {
            $auth = new \Pryaniki\App\Auth();
            return $this->getTemplate($auth);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @throws Exception
     */
    private function getTemplate(Auth $auth): string
    {
        $templatePath = $_SERVER['DOCUMENT_ROOT'] . '/src/View/auth.php';

        if (!file_exists($templatePath)) {
            throw new Exception("Template not found: $templatePath");
        }

        ob_start();
        require $templatePath;
        return ob_get_clean();
    }
}