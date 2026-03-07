<?php

namespace Application\Service;

/**
 * Группирует строки по типам
 */
class CounterGrouper {
    private const GROUP_MAP = [
        'counter_electricity' => 'counter_electricity',
        'skaut-electricity-counter' => 'counter_electricity',
        'counter_cold' => 'counter_water',
        'counter_hot' => 'counter_water',
        'skaut-cold-water-counter' => 'counter_water',
        'skaut-hot-water-counter' => 'counter_water',
        'skaut-heat-counter' => 'counter_heat',
        'counter_heat' => 'counter_heat',
        'counter-gas' => 'counter_gas',
    ];
    
    private const GROUP_TITLES = [
        'counter_water' => 'Водоснабжение',
        'counter_gas' => 'Газоснабжение',
        'counter_heat' => 'Теплоснабжение',
        'counter_electricity' => 'Электроэнергия',
        'counter_other' => 'Другие',
    ];
    
    public function group(array $rows): array {
        $groups = $this->initializeGroups();
        
        foreach ($rows as $row) {
            $groupKey = self::GROUP_MAP[$row['model']] ?? 'counter_other';
            $groups[$groupKey]['items'][] = $row;
        }
        
        return $this->filterEmptyGroups($groups);
    }
    
    private function initializeGroups(): array {
        $groups = [];
        
        foreach (self::GROUP_TITLES as $key => $title) {
            $groups[$key] = ['title' => $title, 'items' => []];
        }
        
        return $groups;
    }
    
    private function filterEmptyGroups(array $groups): array {
        return array_values(array_filter($groups, function($group) {
            return !empty($group['items']);
        }));
    }
}