<?php

namespace Pryaniki\App;

use Exception;

class App
{
    public function run(): string
    {
        try {
            return $this->getTemplate();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @throws Exception
     */
    private function getTemplate(): string
    {
        $templatePath = $_SERVER['DOCUMENT_ROOT'] . '/page-template.php';

        if (!file_exists($templatePath)) {
            throw new Exception("Template not found: $templatePath");
        }

        ob_start();
        require $templatePath;
        return ob_get_clean();
    }
}