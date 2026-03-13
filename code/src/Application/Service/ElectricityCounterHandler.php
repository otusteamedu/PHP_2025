<?php

namespace Application\Service;

class ElectricityCounterHandler implements CounterHandlerInterface {
    private const TARIFF_COUNT = 3;
    
    public function handle(array $counter, array $signals, array $baseRow, bool $isAutomated): array {
        $rows = [];
        
        for ($i = 1; $i <= self::TARIFF_COUNT; $i++) {
            $signalData = $this->collectSignalData($signals, $isAutomated, "kvh-t$i");
            if ($signalData) {
                $rows[] = array_merge($baseRow, $signalData);
            }
        }
        
        return $rows;
    }
    
    private function collectSignalData(array $signals, bool $isAutomated, string $signalName): ?array {
        return SignalDataCollector::collect($signals, $isAutomated, $signalName);
    }
}