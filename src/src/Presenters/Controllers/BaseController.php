<?php

namespace Crowley\App\Presenters\Controllers;

class BaseController
{

    protected string $pageTitle = 'Главная';

    public function render(string $view, array $params = []): void
    {
        $viewPath = dirname(__DIR__)  . '/Views/'  . $view . '.php';
        $layoutPath = dirname(__DIR__)  . '/Views/layout/main.php';

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View {$viewPath} не найден");
        }

        extract($params); // функция просто парсит массив ключ - значение и становится переменная - значение

        ob_start();
        require $viewPath; // получается здесь, я получаю свой файл view/home/index.php
        $content = ob_get_clean();

        $pageTitle = $this->pageTitle;

        require $layoutPath; // а здесь уже подключается основной шаблон

    }

}