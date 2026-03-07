<?php

namespace Application\Service;

class StandardCounterHandler implements CounterHandlerInterface {
    private const DEVICE_NAMES = [
        'counter_cold' => 'Холодная вода',
        'counter_hot' => 'Горячая вода',
        'counter_heat' => 'Тепло',
        'counter-gas' => 'Газ',
        'skaut-cold-water-counter' => 'Холодная вода',
        'skaut-hot-water-counter' => 'Горячая вода',
        'skaut-heat-counter' => 'Тепло',
        'skaut-electricity-counter' => 'Электроэнергия'
    ];
    
    private const SIGNAL_MAP = [
        'counter_cold' => 'm3',
        'counter_hot' => 'm3',
        'svk-15-3-2-md' => 'm3',
        'counter-gas' => 'm3gas',
        'counter_heat' => 'gcal',
        'skaut-cold-water-counter' => 'm3',
        'skaut-hot-water-counter' => 'm3',
        'skaut-heat-counter' => 'gcal',
        'skaut-electricity-counter' => 'kvh-t1'
    ];
    
    public function handle(array $counter, array $signals, array $baseRow, bool $isAutomated): array {
        $rows = [];
        $modelName = $counter['device_model_name'];
        
        // Основной сигнал
        $signalName = self::SIGNAL_MAP[$modelName] ?? $modelName;
        $signalData = SignalDataCollector::collect($signals, $isAutomated, $signalName);
        
        if ($signalData) {
            $row = array_merge($baseRow, $signalData);
            $row['title'] = self::DEVICE_NAMES[$modelName] ?? $counter['device_model_name_rus'];
            $rows[] = $row;
        }
        
        // Дополнительный сигнал ПВС для холодной воды
        if ($modelName === 'counter_cold' && isset($signals['pvs_m3'])) {
            $pvsData = SignalDataCollector::collect($signals, $isAutomated, 'pvs_m3');
            if ($pvsData) {
                $pvsRow = array_merge($baseRow, $pvsData);
                $pvsRow['title'] = $pvsData['title'] ?? $counter['device_model_name_rus'];
                $rows[] = $pvsRow;
            }
        }
        
        return $rows;
    }
}