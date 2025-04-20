<?php

namespace Core;

use Controllers\FileController;

class Router
{
    private array $urlList = [
    '/files/list' => ['GET', [FileController::class, 'listFiles']],
    '/files/get/{id}' => ['GET', [FileController::class, 'fileInfo']],
    '/files/add' => ['POST', [FileController::class, 'addFile']],
    '/directories/add' => ['POST', [FileController::class, 'addDirectory']],
    '/directories/get/{id}' => ['GET', [FileController::class, 'directoryInfo']],
    ];

    /**
     * @return array|null
     */
    public function getRoute(): ?array
    {
        $path = $_SERVER['REQUEST_URI'] ?? '';
        $pathRep = preg_replace('/(\d+)/', '{}', $path);
        foreach ($this->urlList as $url => $methodAction) {
            $urlRep = preg_replace('/{\w+}/', '{}', $url);
            if ($pathRep == $urlRep && $methodAction[0] == $_SERVER['REQUEST_METHOD']) {
                $params = [];
                $pathArray = explode('/', $path);
                foreach ($pathArray as $item) {
                    if (is_numeric($item)) {
                        $params[] = $item;
                    }
                }

                $controller = new $methodAction[1][0]();
                $method = $methodAction[1][1];
                $methodParams = [
                    $params[0] ?? $login ?? '',
                    $params[1] ?? $password ?? ''
                ];

                return [
                    'controller' => $controller,
                    'method' => $method,
                    'methodParams' => $methodParams
                ];
            }
        }
        return null;
    }
}