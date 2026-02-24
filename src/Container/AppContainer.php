<?php

namespace Hafiz\Php2025\Container;

class AppContainer {
    protected array $bindings = [];

    public function bind(string $abstract, callable $creator) {
        $this->bindings[$abstract] = $creator;
    }

    public function make(string $abstract) {
        return $this->bindings[$abstract]($this);
    }
}
