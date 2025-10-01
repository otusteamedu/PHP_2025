<?php

namespace App\Container;

class DIContainer {
    private $definitions = [];
    private $instances = [];
    
    public function set(string $id, callable $definition): void {
        $this->definitions[$id] = $definition;
    }
    
    public function get(string $id) {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }
        
        if (isset($this->definitions[$id])) {
            $this->instances[$id] = $this->definitions[$id]($this);
            return $this->instances[$id];
        }
        
        throw new \Exception("Service not found: " . $id);
    }
}