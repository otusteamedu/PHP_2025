<?php

namespace Application\Service;

class CounterHandlerFactory {
    private const HANDLERS = [
        'counter_electricity' => ElectricityCounterHandler::class,
        'skaut-electricity-counter' => ElectricityCounterHandler::class,
    ];
    
    public static function create(string $modelName): CounterHandlerInterface {
        $handlerClass = self::HANDLERS[$modelName] ?? StandardCounterHandler::class;
        return new $handlerClass();
    }
}