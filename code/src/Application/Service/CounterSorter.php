<?php

namespace Application\Service;

/**
 * Отвечает за упорядочивание счетчиков
 */
class CounterSorter {
    private const PRIORITY_ORDER = [
        'counter_cold',
        'skaut-cold-water-counter',
        'counter_hot', 
        'skaut-hot-water-counter',
        'counter_heat',
        'skaut-heat-counter'
    ];
    
    public static function sort(array $counters): array {
        $sorted = [];
        
        foreach (self::PRIORITY_ORDER as $model) {
            foreach ($counters as $key => $counter) {
                if ($counter['device_model_name'] === $model) {
                    $sorted[] = $counter;
                    unset($counters[$key]);
                }
            }
        }
        
        return array_merge($sorted, array_values($counters));
    }
}