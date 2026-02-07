<?php
define('BASE_PATH', __DIR__);
require BASE_PATH . '/vendor/autoload.php';

use App\Container;

$container = new Container();

// маршрутизация на основе GET-параметров

$controllerName = $_GET['controller'] ?? 'index';
$actionName = $_GET['action'] ?? 'index';

$controllerClass = 'App\\Controllers\\' . $controllerName . 'Controller';
try {
    if (!$container->has($controllerClass)) {
        throw new Exception("Controller '{$controllerClass}' is not registered in the container.");
    }

    $controller = $container->get($controllerClass);

} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    exit;
}

$actionMethod = $actionName . 'Action';

if (!method_exists($controller, $actionMethod)) {
    header("HTTP/1.1 404 Not Found");
    die("Action '{$actionMethod}' not found in controller '{$controllerClass}'.");
}

$params = array_values(array_diff_key($_GET, ['controller' => '', 'action' => '']));

$content = call_user_func_array([$controller, $actionMethod], $params);

echo $content;
