<?php

namespace app\controllers;


abstract class Controller
{
    public function render($template, $params = []) {
        if ($this->useLayout) {
            return $this->renderTemplate("layouts/{$this->layout}",[
                'content' => $this->renderTemplate($template, $params)
            ]);
        } else {
            return $this->renderTemplate($template, $params);
        }
    }

    public function renderTemplate($template, $params = []) {
        ob_start();
        extract($params);
        $templatePath = "../views/" . $template . ".php";
        if (file_exists($templatePath)) {
            include $templatePath;
        }
        return ob_get_clean();

    }
    public function runAction($action = null, $data = null) {
        $this->action = $action ?: $this->defaultAction;
        $method = "action" . ucfirst($this->action);
//        var_dump($this->defaultAction);
        if (method_exists($this, $method)) {

            $this->$method();
        }
        else {
            echo "404";
        }

    }

}