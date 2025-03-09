<?php

namespace App;

use App\Http\Request;
use Closure;
use InvalidArgumentException;

class RequestPipeline
{
    protected Request $request;
    protected array $pipes;
    protected string $method = 'validate';

    /**
     * @param $request
     * @return static
     */
    public static function send($request): static
    {
        $pipeline = new static;

        $pipeline->request = $request;

        return $pipeline;
    }

    /**
     * @param array $pipes
     * @return $this
     */
    public function through(array $pipes): static
    {
        $this->pipes = $pipes;

        return $this;
    }

    /**
     * @param Closure $destination
     * @return mixed
     */
    public function then(Closure $destination): mixed
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            $this->carry(),
            function ($request) use ($destination) {
                return $destination($request);
            }
        );

        return $pipeline($this->request);
    }

    /**
     * @return Request
     */
    public function thenReturn(): Request
    {
        return $this->then(function ($request) {
            return $request;
        });
    }

    /**
     * @return callable
     */
    protected function carry(): callable
    {
        return function ($stack, $pipe) {
            return function ($request) use ($stack, $pipe) {
                if (is_callable($pipe)) {
                    return $pipe($request, $stack);
                } elseif (is_object($pipe)) {
                    return $pipe->{$this->method}($request, $stack);
                } elseif (is_string($pipe) && class_exists($pipe)) {
                    $pipeInstance = new $pipe;
                    return $pipeInstance->{$this->method}($request, $stack);
                } else {
                    throw new InvalidArgumentException('Неверный тип обработчика.');
                }
            };
        };
    }
}