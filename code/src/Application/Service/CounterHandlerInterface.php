<?php

namespace Application\Service;

interface CounterHandlerInterface {
    public function handle(
        array $counter, 
        array $signals, 
        array $baseRow, 
        bool $isAutomated
    ): array;
}