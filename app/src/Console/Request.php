<?php declare(strict_types=1);

namespace App\Console;

/**
 * The console Request represents the environment information for a console application.
 * @package App\Console
 *
 * @property array $params the command line arguments. It does not include the entry script name.
 */
class Request
{
    /**
     * @var array|null
     */
    private ?array $_params = null;

    /**
     * Returns the command line arguments.
     * @return array the command line arguments. It does not include the entry script name.
     */
    public function getParams(): array
    {
        if ($this->_params === null) {
            if (isset($_SERVER['argv'])) {
                $this->_params = $_SERVER['argv'];
                array_shift($this->_params);
            } else {
                $this->_params = [];
            }
        }

        return $this->_params;
    }

    /**
     * Sets the command line arguments.
     * @param array $params the command line arguments
     * @return void
     */
    public function setParams(array $params): void
    {
        $this->_params = $params;
    }

    /**
     * Resolves the current request into a route and the associated parameters.
     * @return array the first element is the route, and the second is the associated parameters.
     */
    public function resolve(): array
    {
        $rawParams = $this->getParams();

        $route = isset($rawParams[0]) ? array_shift($rawParams) : '';

        $params = [];
        foreach ($rawParams as $param) {
            if (preg_match('/^--([\w-]+)(?:=(.*))?$/', $param, $matches)) {
                $params[$matches[1]] = $matches[2] ?? true;
            }
        }

        return [$route, $params];
    }
}
