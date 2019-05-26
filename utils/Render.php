<?php

namespace app\utils;

class Render implements \app\interfaces\IRender
{

    public function renderTemplate($template, $params = []) {

        ob_start();
        extract($params);
        $templatePath = "../views/" . $template . ".php";

        if (file_exists($templatePath)) {
            include $templatePath;
        }
        return ob_get_clean();
    }
}