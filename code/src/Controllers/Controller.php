<?php

namespace App\Controllers;

use App\Domain\Service\Auth\AuthServiceInterface;
use App\Domain\Service\View\ViewInterface;

/**
 * Абстрактный базовый контроллер.
 */
abstract class Controller
{
    protected ViewInterface $view;

    // Получаем зависимости через конструктор
    public function __construct(ViewInterface $view)
    {
        $this->view = $view;
    }

    protected function render(string $template, array $data = []): string
    {
        // Автоматически добавляем authService в данные для шаблона
        return $this->view->render($template, $data);
    }

    public function isAjax(): bool
    {
        return (isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
    }

    public function indexAction()
    {
        echo __CLASS__ . '->' . __METHOD__ . '<br/>';
    }

    public function redirect(string $url): void
    {
        header('Location: ' . $url);
        die;
    }

    public static function url(string $controller, ...$args): string
    {
        $path = implode("/", func_get_args());
        // APP_BASE_URL - плохая практика (глобальная константа), но пока оставим
        return (defined('APP_BASE_URL') ? APP_BASE_URL : '/') . $path;
    }

    public function isPost(): bool
    {
        return !empty($_POST);
    }
}
