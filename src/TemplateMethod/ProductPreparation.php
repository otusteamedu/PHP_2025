<?php

namespace Hafiz\Php2025\TemplateMethod;

abstract class ProductPreparation {
    final public function prepare() {
        $this->preparationStart();
        $this->make();
        $this->preparationEnd();
    }

    protected function preparationStart() {
        echo "Checking preparation conditions...\n";
    }

    protected function preparationEnd() {
        echo "Checking product quality...\n";
    }

    abstract protected function make();
}
