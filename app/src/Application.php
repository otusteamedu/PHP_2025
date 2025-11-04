<?php declare(strict_types=1);

namespace App;

use App\Console\Exceptions\Exception;
use App\Console\Exceptions\UnknownCommandException;
use App\Console\Request;
use App\Controllers\ShopSearchController;
use App\Controllers\ShopStorageController;
use Throwable;

/**
 * Class Application
 * @package App
 */
class Application
{
    /**
     * @var Application
     */
    public static Application $app;
    /**
     * @var array
     */
    private array $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        self::$app = $this;
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    public function run(): mixed
    {
        try {
            return $this->handleRequest(new Request());
        } catch (Throwable $e) {
            echo 'Error: ' . $e->getMessage() . PHP_EOL;
            return 0;
        }
    }

    /**
     * Handles the specified request.
     * @param Request $request the request to be handled
     * @return mixed
     * @throws Console\Exceptions\Exception
     * @throws UnknownCommandException
     */
    private function handleRequest(Request $request): mixed
    {
        list($route, $params) = $request->resolve();
        return $this->runAction($route, $params);
    }

    /**
     * @param string $route
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    private function runAction(string $route, array $params = []): mixed
    {
        $controllerName = null;
        $actionName = null;

        switch ($route) {
            case 'storage/init':
                $controllerName = ShopStorageController::class;
                $actionName = 'actionInit';
                break;
            case 'storage/create':
                $controllerName = ShopStorageController::class;
                $actionName = 'actionCreate';
                break;
            case 'storage/seed':
                $controllerName = ShopStorageController::class;
                $actionName = 'actionSeed';
                break;
            case 'storage/delete':
                $controllerName = ShopStorageController::class;
                $actionName = 'actionDelete';
                break;
            case 'search':
                $controllerName = ShopSearchController::class;
                $actionName = 'actionSearch';
                $params = ['params' => $params];
                break;
        }

        if (!$controllerName || !$actionName) {
            throw new UnknownCommandException($route);
        }

        if (!class_exists($controllerName)) {
            throw new Exception("Controller $controllerName not exist.");
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $actionName)) {
            throw new Exception("Action $actionName in controller $controllerName not exist.");
        }

        return call_user_func_array([$controller, $actionName], $params);
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
