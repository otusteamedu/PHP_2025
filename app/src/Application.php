<?php

declare(strict_types=1);

namespace App;

use App\Controllers\EventController;
use App\Controllers\StorageController;
use App\Http\Exceptions\BadRequestHttpException;
use App\Http\Exceptions\HttpException;
use App\Http\Exceptions\MethodNotAllowedHttpException;
use App\Http\Exceptions\NotFoundHttpException;
use App\Http\Request;
use App\Http\Response;
use DomainException;
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
     * @var Request
     */
    private Request $request;
    /**
     * @var array
     */
    private array $components;

    /**
     *
     */
    public function __construct(array $config)
    {
        self::$app = $this;
        $this->config = $config;
        $this->request = new Request();
        $this->components = $config['components'] ?? [];
    }

    /**
     * @return void
     */
    public function run(): void
    {
        echo $this->handleRequest()->send();
    }

    /**
     * Handles the specified request.
     * @return Response
     */
    private function handleRequest(): Response
    {
        try {
            return $this->runAction();
        } catch (HttpException $e) {
            $statusCode = $e->getStatusCode();

            return Response::create(
                $statusCode,
                [
                    'name' => $e->getName(),
                    'status' => $statusCode,
                    'message' => $e->getMessage(),
                ]
            );
        } catch (Throwable $e) {
            $statusCode = 500;

            return Response::create(
                $statusCode,
                [
                    'name' => 'Internal Server Error',
                    'status' => $statusCode,
                    'message' => $e->getMessage(),
                ]
            );
        }
    }

    /**
     * @return Response
     * @throws MethodNotAllowedHttpException
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    private function runAction(): Response
    {
        $url = $this->request->getUrl();
        $method = $this->request->getMethod();

        switch ($url) {
            case '/storage':
                switch ($method) {
                    case 'DELETE':
                        $controllerName = StorageController::class;
                        $actionName = 'actionClear';
                        break;
                    default:
                        throw new MethodNotAllowedHttpException('This method not allowed');
                }
                break;
            case '/events':
                switch ($method) {
                    case 'POST':
                        $controllerName = EventController::class;
                        $actionName = 'actionCreate';
                        break;
                    case 'GET':
                        $controllerName = EventController::class;
                        $actionName = 'actionSearch';
                        break;
                    default:
                        throw new MethodNotAllowedHttpException('This method not allowed');
                }
                break;
            default:
                throw new NotFoundHttpException('Route undefined');
        }

        if (!class_exists($controllerName)) {
            throw new BadRequestHttpException("Controller $controllerName not exist.");
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $actionName)) {
            throw new BadRequestHttpException("Action $actionName in controller $controllerName not exist.");
        }

        return call_user_func([$controller, $actionName]);
    }

    /**
     * @param string $name
     * @return mixed
     * @throws DomainException
     */
    public function getComponent(string $name): mixed
    {
        if (array_key_exists($name, $this->components)) {
            return new $this->components[$name]();
        }

        throw new DomainException("Component $name not initialized");
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
